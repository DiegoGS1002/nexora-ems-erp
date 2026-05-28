# Módulo de Fluxo de Caixa

## 1. Visão Geral

Controla todas as movimentações financeiras (entradas e saídas) em tempo real, fornecendo visão do caixa disponível e projeções.

### Funcionalidades
- Registro de entradas e saídas
- Saldo atual e projetado
- Categorização por plano de contas
- Conciliação com contas bancárias
- Relatórios e gráficos

---

## 2. Estrutura

### Model: `CashFlow`
```php
protected $fillable = [
    'date',
    'type', // income, expense
    'category_id',
    'description',
    'amount',
    'payment_method',
    'bank_account_id',
    'account_payable_id',
    'account_receivable_id',
    'user_id',
];
```

### Cálculos
```php
// Saldo do dia
$balance = CashFlow::whereDate('date', today())
    ->selectRaw('SUM(CASE WHEN type = "income" THEN amount ELSE -amount END) as total')
    ->value('total');

// Saldo acumulado
$accumulated = CashFlow::where('date', '<=', $date)
    ->get()
    ->sum(fn($cf) => $cf->type === 'income' ? $cf->amount : -$cf->amount);
```

---

## 3. Integrações

- Criado automaticamente ao baixar Contas a Pagar/Receber
- Vincula a Contas Bancárias
- Base para relatórios DRE

---

**Caminho**: `app/Models/CashFlow.php`, `app/Livewire/Financeiro/FluxoCaixa.php`

