# Módulo Financeiro - Contas a Pagar

## 1. Visão Geral

O módulo de Contas a Pagar gerencia todas as obrigações financeiras da empresa com fornecedores e prestadores de serviços. Controla vencimentos, baixas, reagendamentos e integra-se com Pedidos de Compra e Folha de Pagamento.

### Funcionalidades Principais
- Cadastro manual e automático de contas a pagar
- Controle de vencimentos e atrasos
- Baixa de pagamentos (parcial ou total)
- Reagendamento de parcelas
- Recorrência automática
- Integração com compras e RH
- Relatórios gerenciais
- Alertas de contas vencendo

---

## 2. Regras de Negócio

### 2.1 Status das Contas
O sistema gerencia 4 status principais:

1. **Pending** (Pendente): Conta registrada, aguardando pagamento
2. **Overdue** (Vencida): Passou da data de vencimento sem pagamento
3. **Paid** (Paga): Baixa realizada, conta quitada
4. **Canceled** (Cancelada): Conta cancelada (devolução, erro, etc.)

### 2.2 Sincronização Automática de Status
O sistema executa diariamente (via cron ou manualmente) o método `syncOverdueStatus()`:
```php
// Atualiza automaticamente contas pendentes que venceram
AccountPayable::where('status', 'pending')
    ->whereDate('due_date_at', '<', now())
    ->update(['status' => 'overdue']);
```

### 2.3 Baixa de Pagamentos
- **Baixa Total**: `paid_amount` = `amount`, status → `paid`
- **Baixa Parcial**: `paid_amount` < `amount`, status permanece `pending`
- **Multa/Juros**: Pode ser adicionado ao valor pago
- **Desconto**: Pode ser aplicado no pagamento antecipado

### 2.4 Recorrência
Contas marcadas como `is_recurring = true` geram automaticamente nova conta no próximo período:
- Mensal: mesmo dia do próximo mês
- Quinzenal: a cada 15 dias
- Anual: mesmo dia do próximo ano

Exemplo: Aluguel de R$ 5.000,00 todo dia 10

### 2.5 Reagendamento
Permite alterar data de vencimento sem criar nova conta:
```php
$account->reschedule(newDueDate: '2026-06-30', reason: 'Negociação com fornecedor');
```

### 2.6 Origem das Contas
Contas a pagar podem ser criadas por:
- **Manual**: Cadastro direto pelo usuário
- **Pedido de Compra**: Status `RecebidoTotal` gera conta automaticamente
- **Folha de Pagamento**: Status `Paid` gera conta para cada funcionário
- **Importação**: Via API ou planilha

---

## 3. Estrutura Técnica

### 3.1 Model: `App\Models\AccountPayable`

**Caminho**: `app/Models/AccountPayable.php`

#### Propriedades
```php
protected $fillable = [
    'description_title',      // Descrição da conta
    'supplier_id',            // UUID do fornecedor (nullable)
    'chart_of_account_id',    // UUID do plano de contas (nullable)
    'bank_account_id',        // Conta bancária para pagamento
    
    // Valores
    'amount',                 // Valor original da conta
    'paid_amount',            // Valor já pago
    'discount',               // Desconto aplicado
    'addition',               // Multa/juros adicionados
    
    // Datas
    'due_date_at',            // Data de vencimento
    'payment_date',           // Data efetiva do pagamento
    'created_date',           // Data de emissão/criação
    
    // Status e tipo
    'status',                 // Enum: PayableStatus
    'payment_method',         // boleto, pix, transferencia, dinheiro, etc.
    'payment_type',           // unica, parcelada, recorrente
    'installment_number',     // Número da parcela (1/3, 2/3, etc.)
    'total_installments',     // Total de parcelas
    
    // Recorrência
    'is_recurring',           // Se é recorrente
    'recurring_frequency',    // monthly, weekly, yearly
    'recurring_day',          // Dia da recorrência
    'parent_account_id',      // Se foi gerada por recorrência
    
    // Origem
    'origin_type',            // PurchaseOrder, Payroll, Manual
    'origin_id',              // ID da origem
    
    // Observações
    'notes',                  // Observações internas
    'payment_proof',          // Caminho do comprovante
    
    'user_id',                // Quem cadastrou
];
```

#### Casts
```php
protected $casts = [
    'amount'       => 'decimal:2',
    'paid_amount'  => 'decimal:2',
    'discount'     => 'decimal:2',
    'addition'     => 'decimal:2',
    'due_date_at'  => 'date',
    'payment_date' => 'date',
    'created_date' => 'date',
    'status'       => PayableStatus::class,
    'is_recurring' => 'boolean',
];
```

#### Relacionamentos
- `supplier()`: BelongsTo Supplier
- `chartOfAccount()`: BelongsTo PlanOfAccount
- `bankAccount()`: BelongsTo BaccaratAccount
- `user()`: BelongsTo User
- `parent()`: BelongsTo AccountPayable (recorrência)
- `children()`: HasMany AccountPayable (recorrências geradas)

#### Métodos Principais
```php
isPending(): bool
    // Verifica se está pendente

isOverdue(): bool
    // Verifica se está vencida

isPaid(): bool
    // Verifica se está paga

getRemainingAmount(): float
    // Retorna valor restante (amount - paid_amount)

pay(float $paidAmount, string $paymentMethod, ?string $notes = null): bool
    // Realiza baixa do pagamento

partialPay(float $paidAmount, string $paymentMethod): bool
    // Baixa parcial

reschedule(string $newDueDate, ?string $reason = null): bool
    // Reagenda vencimento

cancel(string $reason): bool
    // Cancela a conta

generateRecurring(): ?AccountPayable
    // Gera próxima recorrência (se aplicável)
```

### 3.2 Enum: `App\Enums\PayableStatus`

**Caminho**: `app/Enums/PayableStatus.php`

```php
enum PayableStatus: string
{
    case Pending = 'pending';
    case Overdue = 'overdue';
    case Paid = 'paid';
    case Canceled = 'canceled';
    
    public function label(): string
    {
        return match($this) {
            self::Pending => 'Pendente',
            self::Overdue => 'Vencida',
            self::Paid => 'Paga',
            self::Canceled => 'Cancelada',
        };
    }
    
    public function color(): string
    {
        return match($this) {
            self::Pending => 'blue',
            self::Overdue => 'red',
            self::Paid => 'green',
            self::Canceled => 'gray',
        };
    }
}
```

### 3.3 Service: `App\Services\ContasPagarService`

**Caminho**: `app/Services/ContasPagarService.php`

#### Métodos
```php
createFromPurchaseOrder(PurchaseOrder $order): Collection
    // Cria contas a partir de pedido de compra
    // - Gera parcelas conforme condição de pagamento
    // - Vincula ao fornecedor
    // - Calcula vencimentos

createFromPayroll(Payroll $payroll): Collection
    // Cria contas a partir de folha de pagamento
    // - Uma conta por funcionário
    // - Vincula ao plano de contas "Salários"

syncOverdueStatus(): int
    // Atualiza status de contas vencidas
    // Retorna quantidade de contas atualizadas

processRecurringAccounts(): int
    // Processa contas recorrentes e gera próximas
    // Executado via cron

getAccountsDueSoon(int $days = 7): Collection
    // Retorna contas vencendo nos próximos X dias

getOverdueAccounts(): Collection
    // Retorna contas vencidas

getAccountsBySupplier(string $supplierId): Collection
    // Retorna contas de um fornecedor específico

calculateTotalPending(): float
    // Soma total de contas pendentes

calculateTotalOverdue(): float
    // Soma total de contas vencidas
```

### 3.4 Livewire Component: `App\Livewire\Financeiro\ContasPagar`

**Caminho**: `app/Livewire/Financeiro/ContasPagar.php`

**Funcionalidades**:
- Listagem paginada com múltiplos filtros
- Cards com KPIs (a pagar hoje, na semana, total pendente, pago no mês)
- Modal de criação/edição com abas:
  1. Dados Gerais: Descrição, fornecedor, valores
  2. Datas: Vencimento, pagamento, criação
  3. Pagamento: Método, conta bancária, comprovante
  4. Recorrência: Configuração de repetição
- Ações rápidas:
  - Baixar pagamento
  - Baixa parcial
  - Reagendar
  - Cancelar
  - Visualizar comprovante
- Sincronização automática de vencidos
- Filtros: status, mês, fornecedor, forma de pagamento
- Busca por descrição
- Exportação para Excel/PDF

---

## 4. Rotas

### 4.1 Rotas Web

**Arquivo**: `routes/financeiro.php`

| Método | URI | Nome | Descrição |
|--------|-----|------|-----------|
| GET | `/accounts_payable` | `accounts_payable.index` | Listagem de contas |

### 4.2 Rotas API

**Arquivo**: `routes/api.php`

| Método | URI | Descrição |
|--------|-----|-----------|
| GET | `/api/accounts-payable` | Lista contas a pagar |
| POST | `/api/accounts-payable` | Cria conta |
| GET | `/api/accounts-payable/{id}` | Detalha conta |
| PUT/PATCH | `/api/accounts-payable/{id}` | Atualiza conta |
| DELETE | `/api/accounts-payable/{id}` | Remove conta |
| POST | `/api/accounts-payable/{id}/pay` | Baixa pagamento |
| POST | `/api/accounts-payable/{id}/partial-pay` | Baixa parcial |
| POST | `/api/accounts-payable/{id}/reschedule` | Reagenda vencimento |
| POST | `/api/accounts-payable/{id}/cancel` | Cancela conta |
| GET | `/api/accounts-payable/due-soon` | Contas vencendo em breve |
| GET | `/api/accounts-payable/overdue` | Contas vencidas |

---

## 5. Fluxos de Dados

### 5.1 Criação Manual de Conta

```
1. Usuário acessa /accounts_payable
2. Clica "Nova Conta"
3. Preenche modal:
   - Descrição: "Aluguel Escritório"
   - Fornecedor: Seleciona da lista
   - Valor: R$ 5.000,00
   - Vencimento: 10/06/2026
   - Método de pagamento: Transferência
   - Plano de contas: Despesas > Aluguel
4. Marca como recorrente (mensal, dia 10)
5. Salva
6. Sistema:
   - Cria AccountPayable com status: pending
   - Registra log de auditoria
   - Agenda próxima recorrência
```

### 5.2 Criação Automática via Pedido de Compra

```
1. Pedido de Compra muda status para "RecebidoTotal"
2. Observer detecta mudança
3. Chama ContasPagarService::createFromPurchaseOrder($order)
4. Service:
   - Busca condição de pagamento do pedido
   - Divide total em parcelas
   - Calcula vencimentos (ex: 30/60/90 dias)
   - Cria uma AccountPayable para cada parcela:
     * description_title: "Pedido #1234 - Parcela 1/3"
     * supplier_id: do pedido
     * amount: total / 3
     * due_date_at: data_pedido + 30 dias
     * status: pending
     * origin_type: "PurchaseOrder"
     * origin_id: ID do pedido
5. Retorna collection de contas criadas
```

### 5.3 Baixa de Pagamento

```
1. Usuário localiza conta pendente
2. Clica "Baixar Pagamento"
3. Modal abre:
   - Data do pagamento: hoje (editável)
   - Valor pago: R$ 5.000,00 (pode alterar para parcial)
   - Desconto: R$ 0,00 (se pagou antecipado)
   - Juros/Multa: R$ 0,00 (se pagou atrasado)
   - Método: Transferência
   - Comprovante: upload opcional
4. Confirma
5. Sistema:
   - Atualiza paid_amount
   - Define payment_date
   - Altera status para "paid"
   - Cria CashFlow entry (saída)
   - Registra log
   - Se is_recurring: gera próxima recorrência
```

### 5.4 Sincronização de Vencidos (Cron)

```
# Comando agendado (app/Console/Kernel.php)
$schedule->call(function () {
    $service = app(ContasPagarService::class);
    $updated = $service->syncOverdueStatus();
    
    Log::info("Contas vencidas atualizadas: {$updated}");
    
    // Notifica responsável se há contas vencidas
    if ($updated > 0) {
        Notification::send(
            User::where('is_admin', true)->get(),
            new OverdueAccountsNotification($updated)
        );
    }
})->daily();
```

---

## 6. Integrações com Outros Módulos

### 6.1 Módulo de Compras
**Pedido de Compra → Contas a Pagar**
- Status `RecebidoTotal` gera contas automaticamente
- Parcelas conforme condição de pagamento
- Vínculo com fornecedor

### 6.2 Módulo de RH
**Folha de Pagamento → Contas a Pagar**
- Status `Paid` gera uma conta por funcionário
- Valor: salário líquido
- Vencimento: data de pagamento definida

### 6.3 Módulo Financeiro (Fluxo de Caixa)
**Baixa de Pagamento → CashFlow**
- Toda baixa cria entrada de saída no fluxo de caixa
- Permite reconciliação bancária

### 6.4 Módulo de Plano de Contas
**Categorização**
- Cada conta vincula a uma conta contábil
- Permite relatórios por categoria (aluguel, fornecedores, salários)

---

## 7. Exemplos de Código

### 7.1 Criar Conta Manualmente
```php
use App\Models\AccountPayable;
use App\Enums\PayableStatus;

$account = AccountPayable::create([
    'description_title' => 'Aluguel Escritório - Maio/2026',
    'supplier_id' => $supplier->id,
    'amount' => 5000.00,
    'due_date_at' => '2026-05-10',
    'status' => PayableStatus::Pending,
    'payment_method' => 'transferencia',
    'chart_of_account_id' => $planoAluguel->id,
    'is_recurring' => true,
    'recurring_frequency' => 'monthly',
    'recurring_day' => 10,
    'user_id' => auth()->id(),
]);
```

### 7.2 Baixar Pagamento
```php
$account = AccountPayable::find($id);

$account->pay(
    paidAmount: 5000.00,
    paymentMethod: 'pix',
    notes: 'Pago via PIX chave CNPJ'
);

// Sistema atualiza:
// - paid_amount = 5000.00
// - payment_date = today
// - status = paid
```

### 7.3 Baixa Parcial
```php
$account->partialPay(
    paidAmount: 2500.00,
    paymentMethod: 'boleto'
);

// paid_amount = 2500.00
// status permanece "pending"
// Restante: 2500.00
```

### 7.4 Reagendar Vencimento
```php
$account->reschedule(
    newDueDate: '2026-06-30',
    reason: 'Negociação com fornecedor: prorrogação de 20 dias'
);
```

### 7.5 Consultar Contas Vencendo Esta Semana
```php
$service = app(ContasPagarService::class);
$accountsDueSoon = $service->getAccountsDueSoon(days: 7);

foreach ($accountsDueSoon as $account) {
    echo "{$account->description_title} - Vence em: {$account->due_date_at->format('d/m/Y')}\n";
}
```

### 7.6 Criar Contas a Partir de Pedido de Compra
```php
$order = PurchaseOrder::find($orderId);
$service = app(ContasPagarService::class);

$accounts = $service->createFromPurchaseOrder($order);
// Retorna Collection com 3 AccountPayable (se parcelado em 3x)
```

---

## 8. KPIs e Métricas

### 8.1 KPIs Principais
```php
// A pagar hoje
$todayTotal = AccountPayable::where('status', 'pending')
    ->whereDate('due_date_at', now())
    ->sum('amount');

// A pagar na semana
$weekTotal = AccountPayable::where('status', 'pending')
    ->whereBetween('due_date_at', [now(), now()->addDays(7)])
    ->sum('amount');

// Total pendente
$pendingTotal = AccountPayable::where('status', 'pending')
    ->sum('amount');

// Pago no mês
$paidThisMonth = AccountPayable::where('status', 'paid')
    ->whereMonth('payment_date', now()->month)
    ->sum('paid_amount');

// Vencidas (atrasadas)
$overdueTotal = AccountPayable::where('status', 'overdue')
    ->sum('amount');
```

### 8.2 Análise de Fornecedores
```php
$topSuppliers = AccountPayable::selectRaw('supplier_id, SUM(amount) as total')
    ->where('status', '!=', 'canceled')
    ->groupBy('supplier_id')
    ->orderByDesc('total')
    ->take(10)
    ->with('supplier')
    ->get();
```

---

## 9. Performance e Otimizações

### 9.1 Índices
- `status`: Filtros rápidos
- `due_date_at`: Consultas de vencimento
- `supplier_id`: FK indexada
- `payment_date`: Relatórios
- Composto `(status, due_date_at)`: Consultas de vencidos

### 9.2 Eager Loading
```php
AccountPayable::with(['supplier', 'chartOfAccount', 'user'])
    ->where('status', 'pending')
    ->get();
```

### 9.3 Cache de Totais
```php
$totalPending = Cache::remember('accounts_payable_pending_total', 300, fn() =>
    AccountPayable::where('status', 'pending')->sum('amount')
);
```

---

## 10. Testes

### 10.1 Testes de Feature
```php
test('conta é criada com status pendente', function () {
    $account = AccountPayable::factory()->create();
    
    expect($account->status)->toBe(PayableStatus::Pending);
});

test('baixa de pagamento atualiza status para pago', function () {
    $account = AccountPayable::factory()->create(['amount' => 1000]);
    
    $account->pay(1000, 'pix');
    
    expect($account->fresh()->status)->toBe(PayableStatus::Paid);
    expect($account->paid_amount)->toBe(1000.00);
});

test('conta vencida muda status automaticamente', function () {
    $account = AccountPayable::factory()->create([
        'status' => PayableStatus::Pending,
        'due_date_at' => now()->subDays(5),
    ]);
    
    $service = app(ContasPagarService::class);
    $updated = $service->syncOverdueStatus();
    
    expect($updated)->toBe(1);
    expect($account->fresh()->status)->toBe(PayableStatus::Overdue);
});
```

---

## 11. Comandos Artisan

### 11.1 Sincronizar Vencidos
```bash
php artisan tinker
>>> app(ContasPagarService::class)->syncOverdueStatus();
```

### 11.2 Processar Recorrências
```bash
>>> app(ContasPagarService::class)->processRecurringAccounts();
```

---

## 12. Troubleshooting

### 12.1 Problemas Comuns
- **Status não atualiza**: Executar `syncOverdueStatus()`
- **Recorrência não gera**: Verificar `is_recurring = true`
- **Baixa não funciona**: Verificar se status é `pending` ou `overdue`

### 12.2 Comandos Úteis
```bash
php artisan tinker

# Contas vencidas
>>> AccountPayable::where('status', 'overdue')->count();

# Total a pagar
>>> AccountPayable::where('status', 'pending')->sum('amount');

# Próximas contas (7 dias)
>>> AccountPayable::where('status', 'pending')
      ->whereBetween('due_date_at', [now(), now()->addDays(7)])
      ->get();
```

---

**Caminho do Código**: 
- `app/Models/AccountPayable.php`
- `app/Services/ContasPagarService.php`
- `app/Livewire/Financeiro/ContasPagar.php`
- `routes/financeiro.php`

