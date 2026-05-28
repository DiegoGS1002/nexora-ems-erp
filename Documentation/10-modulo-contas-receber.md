# Módulo Financeiro - Contas a Receber

## 1. Visão Geral

O módulo de Contas a Receber gerencia todos os valores a serem recebidos de clientes, controlando vencimentos, recebimentos, inadimplência e integra-se com Pedidos de Venda e NF-e.

### Funcionalidades Principais
- Cadastro manual e automático de contas a receber
- Controle de vencimentos e inadimplência
- Baixa de recebimentos (parcial ou total)
- Reagendamento de parcelas
- Integração com vendas e fiscal
- Relatórios de recebíveis
- Alertas de inadimplência

---

## 2. Regras de Negócio

### 2.1 Status das Contas
1. **Pending** (Pendente): Aguardando recebimento
2. **Overdue** (Vencida): Passou da data sem recebimento
3. **Received** (Recebida): Valor recebido
4. **Canceled** (Cancelada): Cancelada (devolução, cancelamento de venda)

### 2.2 Sincronização Automática
```php
// Atualiza contas pendentes que venceram
AccountReceivable::where('status', 'pending')
    ->whereDate('due_date_at', '<', now())
    ->update(['status' => 'overdue']);
```

### 2.3 Controle de Inadimplência
Quando conta fica vencida por mais de 30 dias:
- Cliente é marcado como inadimplente
- Bloqueia novos pedidos
- Envia notificações automáticas

### 2.4 Formas de Recebimento
- Dinheiro
- Boleto
- PIX
- Cartão de Crédito
- Cartão de Débito
- Transferência Bancária
- Cheque

---

## 3. Estrutura Técnica

### 3.1 Model: `App\Models\AccountReceivable`

**Caminho**: `app/Models/AccountReceivable.php`

```php
protected $fillable = [
    'description_title',
    'client_id',
    'chart_of_account_id',
    'sales_order_id',
    'amount',
    'received_amount',
    'discount',
    'addition',
    'due_date_at',
    'received_at',
    'payment_method',
    'installment_number',
    'total_installments',
    'status',
    'notes',
    'user_id',
];

protected $casts = [
    'amount' => 'decimal:2',
    'received_amount' => 'decimal:2',
    'due_date_at' => 'date',
    'received_at' => 'date',
    'status' => ReceivableStatus::class,
];
```

### 3.2 Service: `App\Services\ContasReceberService`

**Métodos**:
- `createFromSalesOrder(SalesOrder $order)`: Cria contas de pedido
- `syncOverdueStatus()`: Atualiza status de vencidas
- `markClientAsDefaulter(Client $client)`: Marca cliente inadimplente
- `getOverdueAccounts()`: Lista contas vencidas

### 3.3 Livewire: `App\Livewire\Financeiro\ContasReceber`

**KPIs**:
- A receber hoje
- A receber na semana
- Total pendente
- Recebido no mês
- Vencidas (inadimplência)

---

## 4. Integrações

### 4.1 Pedidos de Venda
Quando pedido é faturado, cria contas a receber automaticamente baseado nas parcelas.

### 4.2 NF-e
Vínculo entre nota fiscal e contas a receber para rastreabilidade.

### 4.3 Fluxo de Caixa
Recebimentos geram entradas no fluxo de caixa.

---

## 5. Exemplos de Código

### 5.1 Criar Conta
```php
AccountReceivable::create([
    'description_title' => 'Pedido #1234 - Parcela 1/3',
    'client_id' => $client->id,
    'sales_order_id' => $order->id,
    'amount' => 1000.00,
    'due_date_at' => now()->addDays(30),
    'status' => ReceivableStatus::Pending,
    'payment_method' => 'boleto',
    'installment_number' => 1,
    'total_installments' => 3,
]);
```

### 5.2 Baixar Recebimento
```php
$account->update([
    'received_amount' => 1000.00,
    'received_at' => now(),
    'status' => ReceivableStatus::Received,
]);
```

### 5.3 Consultar Inadimplência
```php
$overdue = AccountReceivable::where('status', 'overdue')
    ->whereDate('due_date_at', '<', now()->subDays(30))
    ->sum('amount');
```

---

**Caminho do Código**: `app/Models/AccountReceivable.php`, `app/Services/ContasReceberService.php`, `app/Livewire/Financeiro/ContasReceber.php`

