# Módulo de Folha de Pagamento

## 1. Visão Geral

O módulo de Folha de Pagamento automatiza o cálculo de salários, descontos e encargos, gerando holerites e integrando-se com o Financeiro para pagamentos.

### Funcionalidades Principais
- Geração automática de folha mensal
- Cálculo de proventos e descontos
- Encargos (INSS, FGTS, IRRF)
- Horas extras e adicionais
- Faltas e atrasos
- Férias e 13º salário
- Rescisões

---

## 2. Regras de Negócio

### 2.1 Proventos (Créditos)
- Salário base
- Horas extras (50%, 100%)
- Adicional noturno (20%)
- Comissões
- Bonificações
- Vale alimentação
- Vale transporte

### 2.2 Descontos (Débitos)
- INSS (7,5% a 14%)
- IRRF (0% a 27,5%)
- Vale transporte (6%)
- Faltas
- Atrasos
- Plano de saúde
- Adiantamentos

### 2.3 Encargos Patronais
- INSS Patronal (20%)
- FGTS (8%)
- RAT (1% a 3%)
- Terceiros (5,8%)

---

## 3. Estrutura Técnica

### 3.1 Models

**Payroll** (Folha):
```php
protected $fillable = [
    'employee_id',
    'reference_month',
    'reference_year',
    'gross_salary',
    'total_earnings',
    'total_deductions',
    'net_salary',
    'inss_value',
    'irrf_value',
    'fgts_value',
    'status', // draft, closed, paid
];
```

**PayrollItem** (Verbas):
```php
protected $fillable = [
    'payroll_id',
    'item_type', // earning, deduction
    'description',
    'value',
    'quantity',
    'reference',
];
```

### 3.2 Service: `PayrollService`

**Métodos**:
- `generateMonthlyPayroll(month, year)`: Gera folha do mês
- `calculateEmployee(employee, month, year)`: Calcula um funcionário
- `calculateINSS(salary)`: Calcula INSS
- `calculateIRRF(salary, dependents)`: Calcula IRRF
- `closePayroll(payroll)`: Fecha folha (impede edição)
- `markAsPaid(payroll)`: Marca como paga e gera contas

---

## 4. Fluxos

### 4.1 Geração de Folha
```
1. Usuário acessa /payroll
2. Clica "Gerar Folha do Mês"
3. Sistema:
   - Lista funcionários ativos
   - Para cada um:
     * Busca salário base
     * Busca registros de ponto (horas extras, faltas)
     * Calcula proventos
     * Calcula descontos (INSS, IRRF)
     * Calcula líquido
   - Cria Payroll e PayrollItems
4. Status: draft (permite edição)
```

### 4.2 Fechamento
```
1. Revisar valores
2. Clicar "Fechar Folha"
3. Status: closed (não permite mais edição)
4. Gera holerites em PDF
```

### 4.3 Pagamento
```
1. Clicar "Marcar como Paga"
2. Sistema:
   - Cria AccountPayable para cada funcionário
   - Status: paid
   - Registra data de pagamento
```

---

## 5. Exemplos

```php
// Gerar folha
$service = app(PayrollService::class);
$payrolls = $service->generateMonthlyPayroll(month: 5, year: 2026);

// Calcular INSS
$inss = $service->calculateINSS(salary: 3500.00);
// Retorna: 385.99 (alíquota progressiva)

// Fechar folha
$payroll->update(['status' => 'closed']);

// Marcar como paga
$service->markAsPaid($payroll);
```

---

**Caminho**: `app/Models/Payroll.php`, `app/Services/PayrollService.php`, `app/Livewire/Rh/FolhaPagamento.php`

