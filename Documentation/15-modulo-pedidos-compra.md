# Módulo de Pedidos de Compra

## 1. Visão Geral

O módulo de Pedidos de Compra gerencia todo o processo de aquisição, desde a solicitação até o recebimento de mercadorias, integrando-se com Fornecedores, Estoque e Financeiro.

### Funcionalidades Principais
- Criação de pedidos de compra
- Múltiplos itens por pedido
- Fluxo de aprovação
- Controle de recebimento
- Integração com estoque
- Geração de contas a pagar
- Condições de pagamento
- Tipos de frete

---

## 2. Regras de Negócio

### 2.1 Status do Pedido
1. **Draft** (Rascunho): Em elaboração
2. **Aguardando Aprovação**: Enviado para aprovação
3. **Aprovado**: Aprovado, pode ser enviado ao fornecedor
4. **Enviado**: Pedido enviado ao fornecedor
5. **Recebido Parcial**: Parte da mercadoria recebida
6. **Recebido Total**: Tudo recebido
7. **Cancelado**: Pedido cancelado

### 2.2 Fluxo de Aprovação
Pedidos acima de determinado valor requerem aprovação gerencial.

### 2.3 Tipos de Frete
- **CIF** (Cost, Insurance and Freight): Fornecedor paga
- **FOB** (Free On Board): Comprador paga
- **Terceiros**: Transportadora independente
- **Próprio**: Veículo próprio

---

## 3. Estrutura Técnica

### 3.1 Model: `App\Models\PurchaseOrder`

```php
protected $fillable = [
    'order_number',
    'supplier_id',
    'user_id',
    'status',
    'order_date',
    'expected_delivery_date',
    'delivery_date',
    
    // Valores
    'subtotal',
    'discount',
    'freight_value',
    'other_expenses',
    'total_amount',
    
    // Pagamento
    'payment_condition',
    'payment_method',
    'installments',
    
    // Frete
    'freight_type',
    'carrier_id',
    
    // Aprovação
    'approved_by',
    'approved_at',
    'approval_notes',
    
    'notes',
];
```

### 3.2 Model: `PurchaseOrderItem`

```php
protected $fillable = [
    'purchase_order_id',
    'product_id',
    'quantity',
    'unit_price',
    'discount',
    'total',
    'quantity_received',
    'notes',
];
```

### 3.3 Livewire: `App\Livewire\Compras\PedidosCompra`

**Abas do Formulário**:
1. Geral: Fornecedor, data, referência
2. Itens: Produtos e quantidades
3. Pagamento: Condições e parcelas
4. Totais: Cálculos automáticos
5. Logística: Frete e entrega
6. Observações: Notas internas

---

## 4. Integrações

### 4.1 Fornecedores
- Seleciona fornecedor
- Puxa condições de pagamento padrão
- Usa preços cadastrados

### 4.2 Estoque
**Ao receber (Status: RecebidoTotal)**:
- Cria StockMovement (input)
- Incrementa Product.stock
- Atualiza custo médio

### 4.3 Financeiro
**Ao receber**:
- Cria AccountPayable
- Gera parcelas conforme condição de pagamento

---

## 5. Fluxos

### 5.1 Criação
```
1. Usuário cria pedido
2. Adiciona fornecedor e itens
3. Define condições de pagamento
4. Status: draft
5. Envia para aprovação
6. Status: aguardando_aprovacao
```

### 5.2 Aprovação
```
1. Gestor revisa pedido
2. Aprova ou rejeita
3. Se aprovado:
   - Status: aprovado
   - Pode ser enviado ao fornecedor
```

### 5.3 Recebimento
```
1. Mercadoria chega
2. Conferência de quantidades
3. Registra recebimento
4. Sistema:
   - Atualiza quantity_received
   - Se tudo recebido: RecebidoTotal
   - Cria movimentações de estoque
   - Gera contas a pagar
```

---

## 6. Exemplos

```php
PurchaseOrder::create([
    'supplier_id' => $supplier->id,
    'order_date' => now(),
    'status' => 'draft',
    'payment_condition' => '30/60 dias',
    'freight_type' => 'cif',
]);

// Adicionar item
$order->items()->create([
    'product_id' => $product->id,
    'quantity' => 100,
    'unit_price' => 50.00,
    'total' => 5000.00,
]);

// Aprovar
$order->update([
    'status' => 'aprovado',
    'approved_by' => auth()->id(),
    'approved_at' => now(),
]);

// Receber
$order->update(['status' => 'recebido_total']);
```

---

**Caminho**: `app/Models/PurchaseOrder.php`, `app/Livewire/Compras/PedidosCompra.php`

