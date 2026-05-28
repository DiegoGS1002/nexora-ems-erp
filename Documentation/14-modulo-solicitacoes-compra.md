# Módulo de Solicitações de Compra

## 1. Visão Geral

Gerencia requisições de compra internas, permitindo que departamentos solicitem materiais antes da criação do pedido formal.

### Funcionalidades
- Criação de solicitações
- Aprovação por hierarquia
- Conversão em Pedido de Compra
- Status workflow
- Justificativas

---

## 2. Estrutura

### Model: `PurchaseRequisition`
```php
protected $fillable = [
    'requisition_number',
    'requester_id',
    'department',
    'priority',
    'status', // pending, approved, rejected, converted
    'justification',
    'approved_by',
    'approved_at',
];
```

### Itens
```php
PurchaseRequisitionItem::create([
    'purchase_requisition_id' => $req->id,
    'product_id' => $product->id,
    'quantity' => 10,
    'urgency' => 'normal',
]);
```

---

## 3. Fluxo

```
1. Funcionário cria solicitação
2. Gestor aprova/rejeita
3. Se aprovado: Compras converte em Pedido
4. Status: converted
```

---

**Caminho**: `app/Models/PurchaseRequisition.php`, `app/Livewire/Compras/Solicitacoes.php`

