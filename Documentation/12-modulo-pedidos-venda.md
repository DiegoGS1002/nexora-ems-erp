# Módulo de Pedidos de Venda

## 1. Visão Geral

O módulo de Pedidos de Venda é um dos mais complexos do sistema, gerenciando todo o ciclo de vida de uma venda: desde a criação do pedido até a entrega final e faturamento. Este módulo se integra com praticamente todos os outros módulos do ERP.

### Funcionalidades Principais
- Criação e gestão completa de pedidos de venda
- Múltiplos itens por pedido com cálculos automáticos
- Controle de fluxo de status (Draft → Aprovado → Separação → Enviado → Entregue)
- Gestão de pagamentos e parcelas
- Controle de endereços de entrega e cobrança
- Anexos de documentos
- Log completo de auditoria
- Integração com estoque, financeiro e fiscal
- Validação de crédito do cliente

---

## 2. Regras de Negócio

### 2.1 Status do Pedido
O pedido segue um fluxo de status bem definido:

1. **Draft** (Rascunho): Pedido em elaboração, pode ser editado livremente
2. **Aberto**: Pedido criado, aguardando aprovação
3. **Aprovado**: Aprovado para processamento, não pode mais ser editado
4. **EmSeparacao**: Estoque sendo separado, movimentação de estoque ocorre aqui
5. **Enviado**: Pedido despachado para entrega
6. **Entregue**: Cliente recebeu o pedido
7. **Faturado** (Invoiced): NF-e emitida, contas a receber criadas
8. **Cancelado**: Pedido cancelado, estoque devolvido (se já separado)

### 2.2 Validações de Cliente
Antes de criar um pedido, o sistema verifica:
- Cliente deve estar **ativo** (não inativo)
- Se cliente estiver **inadimplente**, requer aprovação gerencial
- **Limite de crédito**: soma de pedidos aprovados + faturados não pode exceder o limite
- Se exceder, pedido fica bloqueado até aprovação gerencial

### 2.3 Cálculo de Totais
O pedido calcula automaticamente:
```
subtotal = soma(item.quantity * item.unit_price)
discount_total = soma(item.discount)
addition_total = soma(item.addition)
tax_total = soma(impostos de todos os itens)
freight_value = valor do frete (se aplicável)
total_amount = subtotal - discount_total + addition_total + tax_total + freight_value
```

### 2.4 Tabelas de Precificação
- Cliente pode ter uma tabela de preços padrão
- Tabela pode ser sobrescrita no pedido
- Preços da tabela são aplicados automaticamente aos itens
- Se tabela tiver regras de quantidade (ex: acima de 100 un), desconto progressivo é aplicado

### 2.5 Reserva de Estoque
- Ao mudar status para **EmSeparacao**, sistema cria `StockMovement` tipo `output`
- Decrementa `Product.stock` de cada item
- Se estoque insuficiente, bloqueia a separação
- Ao cancelar pedido já separado, estoque é devolvido

### 2.6 Integração Financeira
- Ao mudar status para **Faturado**, sistema cria registros em `AccountReceivable`
- Uma parcela para cada installment definido no pagamento
- Status inicial: `pending`
- Vencimentos calculados conforme condição de pagamento

### 2.7 Integração Fiscal
- Ao mudar status para **Faturado**, sistema pode criar `FiscalNote` em rascunho
- Pedido `is_fiscal = true` gera NF-e
- Pedido `is_fiscal = false` não gera nota (venda interna, amostra, etc.)

---

## 3. Estrutura Técnica

### 3.1 Model: `App\Models\SalesOrder`

**Caminho**: `app/Models/SalesOrder.php`

#### Propriedades Principais
```php
protected $fillable = [
    // Relacionamentos
    'client_id',              // UUID do cliente
    'user_id',                // Quem criou o pedido
    'seller_id',              // Vendedor responsável
    'price_table_id',         // Tabela de preços aplicada
    'carrier_id',             // Transportadora
    
    // Dados do pedido
    'order_number',           // Número sequencial (gerado automaticamente)
    'status',                 // Enum SalesOrderStatus
    'operation_type',         // Enum TipoOperacaoVenda
    'sales_channel',          // Enum CanalVenda (Balcao, Online, Telefone, etc.)
    'origin',                 // Enum OrigemPedido (Manual, API, Importação, etc.)
    
    // Valores
    'subtotal',               // Soma dos itens
    'discount_total',         // Total de descontos
    'addition_total',         // Total de acréscimos
    'tax_total',              // Total de impostos
    'freight_value',          // Valor do frete
    'total_amount',           // Valor total final
    
    // Datas
    'order_date',             // Data do pedido
    'expected_delivery_date', // Data prevista de entrega
    'delivery_date',          // Data efetiva de entrega
    'approved_at',            // Data de aprovação
    'canceled_at',            // Data de cancelamento
    
    // Flags
    'is_fiscal',              // Gera NF-e?
    'is_approved',            // Foi aprovado?
    'needs_approval',         // Necessita aprovação gerencial?
    
    // Dados do cliente (snapshot)
    'client_cpf_cnpj',
    'client_ie',
    'client_type',
    'client_credit_limit',
    'client_situation',
    'client_contact_phone',
    'client_contact_email',
    
    // Observações
    'internal_notes',         // Notas internas (não vai para nota fiscal)
    'customer_notes',         // Notas para o cliente
    'fiscal_notes_obs',       // Observações para NF-e
    
    // Logística
    'freight_type',           // CIF, FOB, Terceiros, Próprio
    'company_branch',         // Filial emissora
    'payment_condition',      // Condição de pagamento
];
```

#### Relacionamentos
- `client()`: BelongsTo Client
- `user()`: BelongsTo User (criador)
- `seller()`: BelongsTo User (vendedor)
- `priceTable()`: BelongsTo PriceTable
- `carrier()`: BelongsTo Carrier
- `items()`: HasMany SalesOrderItem
- `addresses()`: HasMany SalesOrderAddress
- `payments()`: HasMany SalesOrderPayment
- `installments()`: HasManyThrough SalesOrderInstallment
- `attachments()`: HasMany SalesOrderAttachment
- `logs()`: HasMany SalesOrderLog

#### Métodos Principais
```php
calculateTotals()           // Recalcula todos os totais
approve(User $user, ?string $reason) // Aprova o pedido
cancel(string $reason)      // Cancela o pedido
canEdit(): bool             // Verifica se pode editar
canCancel(): bool           // Verifica se pode cancelar
changeStatus(SalesOrderStatus $newStatus, ?string $reason) // Altera status
```

### 3.2 Model: `App\Models\SalesOrderItem`

**Caminho**: `app/Models/SalesOrderItem.php`

```php
protected $fillable = [
    'sales_order_id',
    'product_id',
    'sku',                    // Código do produto
    'ean',                    // Código de barras
    'description',            // Descrição do item
    'unit',                   // Unidade de medida
    'quantity',               // Quantidade
    'unit_price',             // Preço unitário
    'discount',               // Desconto em valor
    'discount_percent',       // Desconto em percentual
    'addition',               // Acréscimo em valor
    'addition_percent',       // Acréscimo em percentual
    'total',                  // Total do item (calculado)
    
    // Dados fiscais (se is_fiscal = true)
    'cfop',
    'ncm',
    'cst',
    'csosn',
    'origin',
    'icms_percent',
    'icms_value',
    'ipi_percent',
    'ipi_value',
    'pis_percent',
    'pis_value',
    'cofins_percent',
    'cofins_value',
    
    'notes',                  // Observações do item
];
```

#### Métodos
```php
calculateTotal()            // Calcula total do item
calculateTaxes()            // Calcula impostos (se fiscal)
```

### 3.3 Service: `App\Services\SalesOrderService`

**Caminho**: `app/Services/SalesOrderService.php`

Este service centraliza toda a lógica de negócio de pedidos de venda.

#### Métodos Principais
```php
createOrder(array $data): SalesOrder
    // Cria pedido completo com validações
    // - Valida cliente (ativo, inadimplente, crédito)
    // - Cria pedido principal
    // - Adiciona itens
    // - Adiciona endereços
    // - Adiciona pagamento e parcelas
    // - Calcula totais
    // - Retorna pedido completo

updateOrder(SalesOrder $order, array $data): SalesOrder
    // Atualiza pedido (apenas se status permite)

approveOrder(SalesOrder $order, ?string $reason): bool
    // Aprova pedido com validações

cancelOrder(SalesOrder $order, string $reason): bool
    // Cancela pedido e reverte estoque se necessário

protected validateOrder(array $data): void
    // Validações de cliente e crédito

protected getProductPrice(SalesOrder $order, Product $product, float $quantity): float
    // Busca preço da tabela de precificação ou preço base

protected generateInstallments(SalesOrder $order, SalesOrderPayment $payment): void
    // Gera parcelas automaticamente
```

### 3.4 Observer: `App\Observers\SalesOrderObserver`

**Caminho**: `app/Observers/SalesOrderObserver.php`

Registrado via atributo `#[ObservedBy([SalesOrderObserver::class])]` no model.

#### Eventos Observados
```php
created(SalesOrder $order)
    // - Gera order_number sequencial
    // - Cria log "Pedido criado"
    // - Envia notificação ao vendedor

updated(SalesOrder $order)
    // - Cria log de alteração
    // - Se status mudou, dispara ações específicas

deleted(SalesOrder $order)
    // - Reverte movimentações de estoque
    // - Cancela contas a receber vinculadas
    // - Cria log de exclusão

onSeparation(SalesOrder $order)
    // - Cria StockMovement para cada item
    // - Decrementa Product.stock
    // - Verifica disponibilidade

onInvoiced(SalesOrder $order)
    // - Cria FiscalNote (draft) se is_fiscal = true
    // - Cria AccountReceivable para cada parcela
```

### 3.5 Livewire Components

#### PedidosVenda: `App\Livewire\Vendas\PedidosVenda`
**Caminho**: `app/Livewire/Vendas/PedidosVenda.php`

**Funcionalidades**:
- Listagem de pedidos com filtros avançados
- Busca por número, cliente, vendedor
- Filtro por status, canal, período
- Cards com totais por status
- Ações rápidas: aprovar, cancelar, visualizar
- Export para Excel/PDF

#### PedidoForm: `App\Livewire\Vendas\PedidoForm`
**Caminho**: `app/Livewire/Vendas/PedidoForm.php`

**Funcionalidades**:
- Formulário com 5 abas:
  1. **Cabeçalho**: Cliente, vendedor, datas, canal
  2. **Itens**: Grid de produtos com cálculos em tempo real
  3. **Entrega**: Endereço, transportadora, frete
  4. **Pagamento**: Condição, parcelas, vencimentos
  5. **Observações**: Notas internas, cliente, fiscal
- Busca de produtos com auto-complete
- Cálculo automático de totais
- Validação de estoque em tempo real
- Validação de crédito do cliente

---

## 4. Rotas

### 4.1 Rotas Web

**Arquivo**: `routes/vendas.php`

| Método | URI | Nome | Descrição |
|--------|-----|------|-----------|
| GET | `/vendas/pedidos` | `vendas.pedidos` | Listagem de pedidos |
| GET | `/vendas/pedidos/novo` | `vendas.pedidos.novo` | Criar novo pedido |
| GET | `/vendas/pedidos/{orderId}/editar` | `vendas.pedidos.editar` | Editar pedido |

### 4.2 Rotas API

**Arquivo**: `routes/api.php`, prefixo `/api/sales-orders`

| Método | URI | Descrição |
|--------|-----|-----------|
| GET | `/api/sales-orders` | Lista pedidos (com filtros) |
| POST | `/api/sales-orders` | Cria pedido |
| GET | `/api/sales-orders/statistics` | Estatísticas de vendas |
| POST | `/api/sales-orders/calculate` | Simula cálculo de pedido |
| GET | `/api/sales-orders/{order}` | Detalha pedido |
| PUT | `/api/sales-orders/{order}` | Atualiza pedido |
| DELETE | `/api/sales-orders/{order}` | Remove pedido |
| POST | `/api/sales-orders/{order}/approve` | Aprova pedido |
| POST | `/api/sales-orders/{order}/cancel` | Cancela pedido |
| GET | `/api/sales-orders/{order}/logs` | Logs do pedido |
| GET | `/api/sales-orders/{order}/attachments` | Anexos do pedido |

---

## 5. Fluxos de Dados

### 5.1 Fluxo Completo de Venda

```
┌─────────────────┐
│  1. Criar Pedido│
│     (Draft)     │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  2. Validações  │
│  - Cliente ativo│
│  - Crédito disp.│
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  3. Aberto      │
│  Aguarda aprova.│
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  4. Aprovado    │
│  Libera p/ sep. │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  5. EmSeparacao │
│  Baixa estoque  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  6. Enviado     │
│  Em transporte  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  7. Entregue    │
│  Cliente recebeu│
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  8. Faturado    │
│  - Cria NF-e    │
│  - Cria contas  │
│    a receber    │
└─────────────────┘
```

### 5.2 Criação de Pedido via Service

```php
// 1. Preparar dados
$data = [
    'client_id' => $clientId,
    'seller_id' => auth()->id(),
    'operation_type' => TipoOperacaoVenda::VendaNormal,
    'sales_channel' => CanalVenda::Balcao,
    'is_fiscal' => true,
    'items' => [
        [
            'product_id' => $product1->id,
            'quantity' => 10,
            'unit_price' => 100.00,
            'discount_percent' => 5,
        ],
        [
            'product_id' => $product2->id,
            'quantity' => 5,
            'unit_price' => 200.00,
        ],
    ],
    'addresses' => [
        'delivery' => [
            'zip_code' => '01310-100',
            'street' => 'Av. Paulista',
            'number' => '1000',
            'city' => 'São Paulo',
            'state' => 'SP',
        ],
    ],
    'payment' => [
        'payment_condition' => 'parcelado',
        'payment_method' => 'boleto',
        'installments' => 3,
    ],
];

// 2. Chamar service
$order = app(SalesOrderService::class)->createOrder($data);

// 3. Sistema executa:
//    - Valida cliente
//    - Cria pedido
//    - Adiciona itens (busca preço da tabela)
//    - Adiciona endereços
//    - Gera parcelas automaticamente
//    - Calcula totais
//    - Retorna pedido completo com relacionamentos
```

### 5.3 Mudança de Status: EmSeparacao

```
1. Usuário clica "Iniciar Separação"
2. Sistema chama changeStatus(SalesOrderStatus::EmSeparacao)
3. Observer detecta mudança e dispara onSeparation()
4. Para cada item do pedido:
   a. Verifica se Product.stock >= item.quantity
   b. Se insuficiente, lança Exception
   c. Cria StockMovement:
      - type: output
      - quantity: item.quantity
      - origin: "Pedido de Venda #{order_number}"
   d. Decrementa Product.stock
5. Cria log: "Pedido em separação"
6. Notifica separador
```

### 5.4 Faturamento

```
1. Usuário clica "Faturar Pedido"
2. Sistema chama changeStatus(SalesOrderStatus::Invoiced)
3. Observer detecta mudança e dispara onInvoiced()
4. Se is_fiscal = true:
   a. Cria FiscalNote (status: draft)
   b. Copia dados do pedido para nota
   c. Copia itens com informações fiscais
5. Para cada parcela (installment):
   a. Cria AccountReceivable:
      - client_id
      - description_title: "Pedido #{order_number} - Parcela {X}/{Y}"
      - amount: valor da parcela
      - due_date_at: data de vencimento
      - status: pending
6. Cria log: "Pedido faturado"
7. Envia notificação ao cliente
```

---

## 6. Integrações com Outros Módulos

### 6.1 Módulo de Clientes
- Busca dados do cliente (nome, CPF/CNPJ, endereço)
- Verifica situação (ativo/inadimplente)
- Valida limite de crédito
- Aplica tabela de preços padrão

### 6.2 Módulo de Produtos
- Busca produtos para adicionar ao pedido
- Obtém preço de venda
- Busca informações fiscais (NCM, CFOP)
- Verifica disponibilidade em estoque

### 6.3 Módulo de Estoque
- Status **EmSeparacao**: cria StockMovement (output), decrementa stock
- Cancelamento: reverte movimentação, incrementa stock

### 6.4 Módulo Financeiro
- Status **Faturado**: cria AccountReceivable para cada parcela
- Vínculo entre parcela e pedido
- Baixa de recebimento atualiza status da parcela

### 6.5 Módulo Fiscal
- Status **Faturado**: cria FiscalNote (draft)
- Usuário pode transmitir NF-e pela interface fiscal
- Copia informações tributárias dos itens

### 6.6 Módulo de Logística
- Vínculo com SchedulingOfDeliveries (agendamento)
- Status **Entregue**: atualizado pela logística ao confirmar entrega

---

## 7. Enums

### 7.1 `App\Enums\SalesOrderStatus`
```php
enum SalesOrderStatus: string
{
    case Draft = 'draft';
    case Aberto = 'aberto';
    case Aprovado = 'aprovado';
    case EmSeparacao = 'em_separacao';
    case Enviado = 'enviado';
    case Entregue = 'entregue';
    case Invoiced = 'invoiced';       // Faturado
    case Cancelado = 'cancelado';
}
```

### 7.2 `App\Enums\TipoOperacaoVenda`
```php
enum TipoOperacaoVenda: string
{
    case VendaNormal = 'venda_normal';
    case Devolucao = 'devolucao';
    case Bonificacao = 'bonificacao';
    case Amostra = 'amostra';
    case Consignacao = 'consignacao';
}
```

### 7.3 `App\Enums\CanalVenda`
```php
enum CanalVenda: string
{
    case Balcao = 'balcao';
    case Telefone = 'telefone';
    case Online = 'online';
    case Representante = 'representante';
    case Marketplace = 'marketplace';
}
```

---

## 8. Exemplos de Código

### 8.1 Criar Pedido Simples
```php
use App\Services\SalesOrderService;

$service = app(SalesOrderService::class);

$order = $service->createOrder([
    'client_id' => $client->id,
    'items' => [
        [
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 150.00,
        ],
    ],
]);
```

### 8.2 Aprovar Pedido
```php
$order = SalesOrder::find($orderId);
$service = app(SalesOrderService::class);

$service->approveOrder($order, 'Crédito aprovado pela gerência');
```

### 8.3 Cancelar Pedido
```php
$service->cancelOrder($order, 'Cliente solicitou cancelamento');
```

### 8.4 Consultar Pedidos por Cliente
```php
$pedidos = SalesOrder::where('client_id', $clientId)
    ->whereIn('status', [
        SalesOrderStatus::Aprovado,
        SalesOrderStatus::EmSeparacao,
        SalesOrderStatus::Enviado,
    ])
    ->with(['items', 'payments'])
    ->get();
```

---

## 9. Performance e Otimizações

### 9.1 Índices
- `order_number`: Índice único
- `client_id`: FK indexada
- `status`: Índice para filtros
- `order_date`: Índice para relatórios
- `seller_id`: FK indexada

### 9.2 Eager Loading
```php
SalesOrder::with([
    'client',
    'seller',
    'items.product',
    'payments.installments',
    'addresses',
    'logs',
])->get();
```

### 9.3 Transações
Todas as operações críticas usam `DB::transaction()`:
```php
DB::transaction(function () {
    // Cria pedido
    // Adiciona itens
    // Gera parcelas
    // Calcula totais
});
```

---

## 10. Testes

### 10.1 Testes de Feature
- `SalesOrderCreationTest`
- `SalesOrderApprovalTest`
- `SalesOrderStockIntegrationTest`
- `SalesOrderFinancialIntegrationTest`

### 10.2 Exemplo de Teste
```php
test('pedido de cliente inadimplente requer aprovação', function () {
    $client = Client::factory()->create(['situation' => 'defaulter']);
    $service = app(SalesOrderService::class);
    
    expect(function () use ($service, $client) {
        $service->createOrder(['client_id' => $client->id]);
    })->toThrow(Exception::class, 'inadimplente');
});
```

---

**Caminho do Código**: 
- `app/Models/SalesOrder.php`
- `app/Services/SalesOrderService.php`
- `app/Observers/SalesOrderObserver.php`
- `app/Livewire/Vendas/`
- `routes/vendas.php`, `routes/api.php`

