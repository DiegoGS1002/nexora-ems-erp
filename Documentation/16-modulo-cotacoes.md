# Módulo de Cotações

## 1. Visão Geral

Permite solicitar cotações a múltiplos fornecedores e comparar propostas para escolher a melhor opção.

### Funcionalidades
- Criação de cotação com produtos
- Envio para múltiplos fornecedores
- Recebimento de propostas
- Comparativo de preços
- Conversão em Pedido de Compra

---

## 2. Estrutura

### Model: `Cotacao`
```php
protected $fillable = [
    'cotacao_number',
    'user_id',
    'status', // draft, sent, received, closed
    'deadline',
    'notes',
];
```

### Fornecedores Convidados
```php
CotacaoSupplier::create([
    'cotacao_id' => $cotacao->id,
    'supplier_id' => $supplier->id,
    'status' => 'sent',
    'sent_at' => now(),
]);
```

### Propostas
```php
CotacaoProposal::create([
    'cotacao_id' => $cotacao->id,
    'supplier_id' => $supplier->id,
    'total_value' => 5000.00,
    'delivery_time_days' => 7,
    'payment_condition' => '30 dias',
    'items' => [...], // JSON com preços por produto
]);
```

---

## 3. Fluxo

```
1. Criar cotação com produtos
2. Selecionar fornecedores
3. Enviar via email
4. Fornecedores respondem
5. Comparar propostas
6. Escolher vencedor
7. Converter em Pedido de Compra
```

---

**Caminho**: `app/Models/Cotacao.php`, `app/Livewire/Compras/Cotacoes.php`

