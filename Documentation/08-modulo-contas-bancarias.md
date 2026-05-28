# Módulo de Contas Bancárias

## 1. Visão Geral

Gerencia as contas bancárias da empresa para controle de saldos, conciliação e transações.

### Funcionalidades
- Cadastro de contas
- Saldo atual
- Extrato de movimentações
- Conciliação bancária
- Transferências entre contas

---

## 2. Estrutura

### Model: `BankAccount`
```php
protected $fillable = [
    'bank_name',
    'bank_code',
    'agency',
    'account_number',
    'account_type', // corrente, poupanca
    'balance',
    'is_default',
    'is_active',
];
```

### Movimentações
```php
BankAccountTransaction::create([
    'bank_account_id' => $account->id,
    'type' => 'credit', // ou debit
    'amount' => 1000.00,
    'description' => 'Recebimento de cliente',
]);
```

---

## 3. Integrações

- Contas a Pagar: Débito ao pagar
- Contas a Receber: Crédito ao receber
- Fluxo de Caixa: Entradas e saídas

---

**Caminho**: `app/Models/BankAccount.php`, `app/Livewire/Financeiro/ContasBancarias/`

