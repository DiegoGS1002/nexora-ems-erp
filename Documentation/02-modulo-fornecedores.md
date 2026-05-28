# Módulo de Fornecedores

## 1. Visão Geral

O módulo de Fornecedores gerencia o cadastro de empresas e pessoas que fornecem produtos ou serviços para a empresa. É fundamental para o módulo de Compras e integra-se diretamente com Produtos, Estoque e Financeiro.

### Funcionalidades Principais
- Cadastro completo de fornecedores (PF e PJ)
- Gestão de produtos por fornecedor
- Controle de condições de pagamento
- Avaliação de fornecedores
- Histórico de compras
- Prazo de entrega por produto
- Múltiplos contatos por fornecedor

---

## 2. Regras de Negócio

### 2.1 Tipos de Fornecedor
- **Pessoa Física**: Fornecedores autônomos, MEI
- **Pessoa Jurídica**: Empresas fornecedoras

### 2.2 Status do Fornecedor
- **Ativo**: Pode receber pedidos de compra
- **Inativo**: Bloqueado para novos pedidos
- **Em Avaliação**: Fornecedor novo em período de teste
- **Bloqueado**: Problemas de qualidade ou inadimplência

### 2.3 Avaliação de Fornecedores
O sistema avalia fornecedores com base em:
- **Qualidade dos Produtos** (0-5 estrelas)
- **Pontualidade na Entrega** (0-5 estrelas)
- **Atendimento** (0-5 estrelas)
- **Preço** (0-5 estrelas)
- **Média Geral**: calculada automaticamente

### 2.4 Condições de Pagamento
- Definidas por fornecedor
- Podem ser sobrescritas no pedido de compra
- Exemplos: À vista, 30/60/90 dias, parcelado

### 2.5 Produtos do Fornecedor
- Cada produto pode ter múltiplos fornecedores
- Tabela pivot `product_supplier` armazena:
  - Código do produto no fornecedor
  - Preço de custo específico
  - Prazo de entrega (dias)
  - Fornecedor preferencial (flag)

---

## 3. Estrutura Técnica

### 3.1 Model: `App\Models\Supplier`

**Caminho**: `app/Models/Supplier.php`

#### Propriedades
```php
protected $fillable = [
    'tipo_pessoa',           // Enum: TipoPessoa (fisica, juridica)
    'name',                  // Razão social ou nome completo
    'social_name',           // Nome fantasia
    'taxNumber',             // CPF ou CNPJ
    'inscricao_estadual',    // IE (apenas PJ)
    'inscricao_municipal',   // IM (serviços)
    'email',
    'phone_number',
    'mobile_number',
    'website',               // Site do fornecedor
    
    // Endereço
    'address_zip_code',
    'address_street',
    'address_number',
    'address_complement',
    'address_district',
    'address_city',
    'address_state',
    'address_country',
    
    // Dados bancários
    'bank_name',
    'bank_agency',
    'bank_account',
    'bank_account_type',     // corrente, poupanca
    'pix_key',
    'pix_key_type',          // cpf, cnpj, email, telefone, aleatoria
    
    // Condições comerciais
    'payment_condition',     // Condição de pagamento padrão
    'delivery_time_days',    // Prazo médio de entrega (dias)
    'minimum_order_value',   // Valor mínimo de pedido
    
    // Avaliação
    'rating_quality',        // 0-5
    'rating_delivery',       // 0-5
    'rating_service',        // 0-5
    'rating_price',          // 0-5
    'rating_average',        // Calculado automaticamente
    
    // Status
    'status',                // active, inactive, under_evaluation, blocked
    'blocked_reason',        // Motivo do bloqueio
    
    // Observações
    'notes',                 // Observações internas
    
    'is_active',             // Ativo/Inativo
];
```

#### Casts
```php
protected $casts = [
    'tipo_pessoa'         => TipoPessoa::class,
    'minimum_order_value' => 'decimal:2',
    'rating_quality'      => 'decimal:1',
    'rating_delivery'     => 'decimal:1',
    'rating_service'      => 'decimal:1',
    'rating_price'        => 'decimal:1',
    'rating_average'      => 'decimal:1',
    'is_active'           => 'boolean',
];
```

#### Relacionamentos
- `products()`: BelongsToMany Product (via `product_supplier`)
- `purchaseOrders()`: HasMany PurchaseOrder
- `purchaseRequisitions()`: HasMany PurchaseRequisition
- `cotacoes()`: HasMany Cotacao

#### Métodos Principais
```php
calculateRatingAverage(): void
    // Calcula média das avaliações

isActive(): bool
    // Verifica se fornecedor está ativo

isBlocked(): bool
    // Verifica se fornecedor está bloqueado

canReceiveOrders(): bool
    // Verifica se pode receber pedidos (ativo e não bloqueado)

getProductPrice(Product $product): ?float
    // Retorna preço do produto para este fornecedor

getProductDeliveryTime(Product $product): ?int
    // Retorna prazo de entrega do produto (dias)
```

### 3.2 Tabela Pivot: `product_supplier`

```php
Schema::create('product_supplier', function (Blueprint $table) {
    $table->id();
    $table->uuid('product_id');
    $table->uuid('supplier_id');
    $table->string('supplier_code')->nullable();      // Código no fornecedor
    $table->decimal('cost_price', 10, 2);             // Preço de custo
    $table->integer('delivery_time_days')->default(0); // Prazo (dias)
    $table->boolean('is_preferred')->default(false);  // Preferencial
    $table->date('last_purchase_date')->nullable();   // Última compra
    $table->decimal('last_purchase_price', 10, 2)->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
    
    $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
    $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
    
    $table->unique(['product_id', 'supplier_id']);
});
```

### 3.3 Livewire Components

#### Index: `App\Livewire\Cadastro\Fornecedores\Index`
**Caminho**: `app/Livewire/Cadastro/Fornecedores/Index.php`

**Funcionalidades**:
- Listagem paginada de fornecedores
- Busca por nome, CNPJ/CPF, e-mail
- Filtro por status (ativo, inativo, bloqueado)
- Ordenação por avaliação
- Cards com estatísticas (total, ativos, bloqueados)
- Indicador visual de avaliação (estrelas)

#### Form: `App\Livewire\Cadastro\Fornecedores\Form`
**Caminho**: `app/Livewire/Cadastro/Fornecedores/Form.php`

**Funcionalidades**:
- Formulário com 5 abas:
  1. **Dados Gerais**: Nome, CNPJ/CPF, IE, contatos
  2. **Endereço**: CEP, rua, número, cidade, estado
  3. **Dados Bancários**: Banco, agência, conta, PIX
  4. **Condições Comerciais**: Pagamento, prazo, valor mínimo
  5. **Avaliação e Observações**: Ratings, status, notas
- Consulta automática de CNPJ via BrasilAPI
- Busca de endereço por CEP
- Cálculo automático de média de avaliação

### 3.4 Controller: `App\Http\Controllers\SupplierController`

**Caminho**: `app/Http/Controllers/SupplierController.php`

#### Métodos
- `print()`: Gera relatório PDF de fornecedores

---

## 4. Rotas

### 4.1 Rotas Web

**Arquivo**: `routes/cadastro.php`

| Método | URI | Nome | Descrição |
|--------|-----|------|-----------|
| GET | `/suppliers` | `suppliers.index` | Listagem de fornecedores |
| GET | `/suppliers/create` | `suppliers.create` | Formulário de criação |
| GET | `/suppliers/{supplier}/edit` | `suppliers.edit` | Formulário de edição |
| GET | `/suppliers/print` | `suppliers.print` | Relatório PDF |

### 4.2 Rotas API

**Arquivo**: `routes/api.php`

| Método | URI | Descrição |
|--------|-----|-----------|
| GET | `/api/suppliers` | Lista fornecedores |
| POST | `/api/suppliers` | Cria fornecedor |
| GET | `/api/suppliers/{id}` | Detalha fornecedor |
| PUT/PATCH | `/api/suppliers/{id}` | Atualiza fornecedor |
| DELETE | `/api/suppliers/{id}` | Remove fornecedor |
| GET | `/api/suppliers/{id}/products` | Produtos do fornecedor |
| POST | `/api/suppliers/{id}/evaluate` | Avalia fornecedor |

---

## 5. Fluxos de Dados

### 5.1 Criação de Fornecedor

```
1. Usuário acessa /suppliers/create
2. Preenche dados básicos (CNPJ, nome, contatos)
3. Sistema pode buscar dados por CNPJ (BrasilAPI)
4. Preenche endereço (busca automática por CEP)
5. Configura dados bancários (PIX opcional)
6. Define condições comerciais
7. Sistema salva fornecedor com UUID
8. Registra log de auditoria
9. Redireciona para listagem
```

### 5.2 Vínculo Produto-Fornecedor

```
1. Ao criar/editar produto
2. Usuário acessa aba "Fornecedores"
3. Adiciona fornecedor via modal:
   - Seleciona fornecedor
   - Informa código no fornecedor (opcional)
   - Define preço de custo
   - Define prazo de entrega (dias)
   - Marca como preferencial (opcional)
4. Sistema salva em product_supplier
5. Histórico de preços é mantido
```

### 5.3 Avaliação de Fornecedor

```
1. Após recebimento de mercadoria (Pedido de Compra)
2. Usuário avalia fornecedor em 4 critérios:
   - Qualidade (0-5 estrelas)
   - Pontualidade (0-5 estrelas)
   - Atendimento (0-5 estrelas)
   - Preço (0-5 estrelas)
3. Sistema calcula média:
   rating_average = (quality + delivery + service + price) / 4
4. Salva avaliação
5. Atualiza rating_average do fornecedor
```

### 5.4 Bloqueio de Fornecedor

```
1. Usuário identifica problema (qualidade, atraso recorrente)
2. Acessa edição do fornecedor
3. Altera status para "blocked"
4. Informa motivo do bloqueio
5. Sistema:
   - Bloqueia novos pedidos de compra
   - Mantém pedidos ativos inalterados
   - Registra log de auditoria
   - Notifica responsáveis
```

---

## 6. Integrações com Outros Módulos

### 6.1 Módulo de Produtos
- **Vínculo N:N**: Produtos podem ter múltiplos fornecedores
- **Preço de Custo**: Cada fornecedor define seu preço
- **Fornecedor Preferencial**: Flag para sugestão em cotações

### 6.2 Módulo de Compras
- **Pedidos de Compra**: Todo pedido requer um fornecedor
- **Cotações**: Sistema envia cotação para fornecedores do produto
- **Recebimento**: Atualiza histórico (última compra, último preço)

### 6.3 Módulo Financeiro
- **Contas a Pagar**: Vincula fornecedor à conta
- **Condição de Pagamento**: Define prazo e parcelas
- **Dados Bancários**: Para pagamentos via transferência/PIX

### 6.4 Módulo de Estoque
- **Recebimento de Mercadoria**: Vincula movimentação ao fornecedor
- **Rastreabilidade**: Histórico de entradas por fornecedor

---

## 7. Exemplos de Código

### 7.1 Criar Fornecedor
```php
use App\Models\Supplier;
use App\Enums\TipoPessoa;

$supplier = Supplier::create([
    'tipo_pessoa' => TipoPessoa::Juridica,
    'name' => 'Distribuidora ABC Ltda',
    'social_name' => 'ABC Distribuidora',
    'taxNumber' => '12.345.678/0001-99',
    'inscricao_estadual' => '123456789',
    'email' => 'contato@abc.com.br',
    'phone_number' => '(11) 3456-7890',
    'payment_condition' => '30/60 dias',
    'delivery_time_days' => 7,
    'minimum_order_value' => 500.00,
    'status' => 'active',
    'is_active' => true,
]);
```

### 7.2 Vincular Produto ao Fornecedor
```php
$product = Product::find($productId);
$supplier = Supplier::find($supplierId);

$product->suppliers()->attach($supplier->id, [
    'supplier_code' => 'ABC-PROD-001',
    'cost_price' => 50.00,
    'delivery_time_days' => 5,
    'is_preferred' => true,
]);
```

### 7.3 Buscar Fornecedor Preferencial de um Produto
```php
$preferredSupplier = $product->suppliers()
    ->wherePivot('is_preferred', true)
    ->first();

if ($preferredSupplier) {
    $costPrice = $preferredSupplier->pivot->cost_price;
    $deliveryTime = $preferredSupplier->pivot->delivery_time_days;
}
```

### 7.4 Avaliar Fornecedor
```php
$supplier->update([
    'rating_quality' => 4.5,
    'rating_delivery' => 5.0,
    'rating_service' => 4.0,
    'rating_price' => 4.5,
]);

$supplier->calculateRatingAverage();
// rating_average = 4.5
```

### 7.5 Listar Fornecedores Ativos com Boa Avaliação
```php
$topSuppliers = Supplier::where('is_active', true)
    ->where('rating_average', '>=', 4.0)
    ->orderByDesc('rating_average')
    ->get();
```

### 7.6 Bloquear Fornecedor
```php
$supplier->update([
    'status' => 'blocked',
    'blocked_reason' => 'Qualidade dos produtos abaixo do esperado',
    'is_active' => false,
]);

LogService::warning(
    'FORNECEDOR_BLOQUEADO',
    "Fornecedor {$supplier->name} bloqueado",
    'Compras',
    ['supplier_id' => $supplier->id, 'reason' => $supplier->blocked_reason]
);
```

---

## 8. Validações

### 8.1 Validações de Formulário
```php
'tipo_pessoa' => 'required|in:fisica,juridica',
'name' => 'required|string|max:255',
'taxNumber' => 'required|string|max:18|unique:suppliers,taxNumber',
'email' => 'nullable|email|max:255',
'phone_number' => 'nullable|string|max:20',
'payment_condition' => 'nullable|string|max:100',
'delivery_time_days' => 'nullable|integer|min:0|max:365',
'minimum_order_value' => 'nullable|numeric|min:0',
'rating_quality' => 'nullable|numeric|min:0|max:5',
'rating_delivery' => 'nullable|numeric|min:0|max:5',
'rating_service' => 'nullable|numeric|min:0|max:5',
'rating_price' => 'nullable|numeric|min:0|max:5',
```

### 8.2 Validações de Negócio
```php
// Ao criar pedido de compra
if (!$supplier->canReceiveOrders()) {
    throw new Exception('Fornecedor bloqueado ou inativo');
}

// Ao vincular produto
if ($costPrice <= 0) {
    throw new Exception('Preço de custo deve ser maior que zero');
}
```

---

## 9. Relatórios e Análises

### 9.1 Ranking de Fornecedores
```php
$ranking = Supplier::where('is_active', true)
    ->orderByDesc('rating_average')
    ->take(10)
    ->get();
```

### 9.2 Fornecedores com Atrasos Recorrentes
```php
$delayedSuppliers = Supplier::where('rating_delivery', '<', 3.0)
    ->where('is_active', true)
    ->get();
```

### 9.3 Histórico de Compras por Fornecedor
```php
$purchaseHistory = PurchaseOrder::where('supplier_id', $supplierId)
    ->with('items.product')
    ->orderByDesc('created_at')
    ->paginate(20);
```

---

## 10. Performance e Otimizações

### 10.1 Índices
- `taxNumber`: Índice único
- `status`: Índice para filtros
- `rating_average`: Índice para ordenação
- `is_active`: Índice booleano

### 10.2 Eager Loading
```php
Supplier::with([
    'products',
    'purchaseOrders' => fn($q) => $q->latest()->take(5),
])->get();
```

### 10.3 Cache de Fornecedores Preferenciais
```php
$preferredSuppliers = Cache::remember('preferred_suppliers', 3600, fn() =>
    Supplier::where('is_active', true)
        ->where('rating_average', '>=', 4.5)
        ->get()
);
```

---

## 11. Testes

### 11.1 Testes de Feature
- `SupplierCreationTest`: Criação com validações
- `SupplierRatingTest`: Cálculo de média
- `SupplierBlockingTest`: Bloqueio e desbloqueio
- `ProductSupplierTest`: Vínculo produto-fornecedor

### 11.2 Exemplo de Teste
```php
test('fornecedor bloqueado não pode receber pedidos', function () {
    $supplier = Supplier::factory()->create(['status' => 'blocked']);
    
    expect($supplier->canReceiveOrders())->toBeFalse();
});

test('média de avaliação é calculada corretamente', function () {
    $supplier = Supplier::factory()->create([
        'rating_quality' => 5.0,
        'rating_delivery' => 4.0,
        'rating_service' => 4.5,
        'rating_price' => 3.5,
    ]);
    
    $supplier->calculateRatingAverage();
    
    expect($supplier->rating_average)->toBe(4.25);
});
```

---

## 12. Manutenção e Troubleshooting

### 12.1 Problemas Comuns
- **CNPJ duplicado**: Verificar se fornecedor já existe
- **Avaliação não atualiza**: Chamar `calculateRatingAverage()`
- **Pedido bloqueado**: Verificar status do fornecedor

### 12.2 Comandos Úteis
```bash
# Listar fornecedores bloqueados
php artisan tinker
>>> Supplier::where('status', 'blocked')->count();

# Fornecedores sem avaliação
>>> Supplier::whereNull('rating_average')->count();

# Top 10 fornecedores
>>> Supplier::orderByDesc('rating_average')->take(10)->pluck('name', 'rating_average');
```

---

## 13. Boas Práticas

### 13.1 Sempre Avaliar Fornecedores
Após cada recebimento, solicitar avaliação para manter dados atualizados.

### 13.2 Manter Múltiplos Fornecedores
Para produtos críticos, cadastrar pelo menos 2 fornecedores ativos.

### 13.3 Revisar Fornecedores Periodicamente
- Mensal: Verificar fornecedores com rating < 3.0
- Trimestral: Avaliar custo-benefício
- Anual: Negociar novas condições comerciais

### 13.4 Documentar Bloqueios
Sempre informar motivo detalhado ao bloquear fornecedor.

---

**Caminho do Código**: `app/Models/Supplier.php`, `app/Livewire/Cadastro/Fornecedores/`, `routes/cadastro.php`

