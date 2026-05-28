# Módulo de Funcionários

## 1. Visão Geral

O módulo de Funcionários gerencia o cadastro completo de colaboradores da empresa, incluindo dados pessoais, contratuais, bancários e documentos. É a base para os módulos de RH (Folha, Ponto, Jornada).

### Funcionalidades Principais
- Cadastro completo de funcionários
- Dados contratuais (cargo, salário, data de admissão)
- Dados bancários para pagamento
- Documentos (RG, CPF, CTPS, PIS)
- Dependentes
- Histórico de cargos e salários
- Férias e benefícios
- Status (ativo, afastado, demitido)

---

## 2. Regras de Negócio

### 2.1 Status do Funcionário
- **Ativo**: Trabalhando normalmente
- **Afastado**: Licença médica, maternidade, etc.
- **Férias**: Em período de férias
- **Demitido**: Desligado da empresa
- **Suspenso**: Suspensão disciplinar

### 2.2 Tipos de Contrato
- CLT
- PJ (Pessoa Jurídica)
- Estagiário
- Temporário
- Autônomo

### 2.3 Cálculo de Férias
- Direito a 30 dias após 12 meses trabalhados
- Pode dividir em até 3 períodos
- 1/3 de adicional sobre o salário

---

## 3. Estrutura Técnica

### 3.1 Model: `App\Models\Employees`

```php
protected $fillable = [
    'name',
    'email',
    'cpf',
    'rg',
    'birth_date',
    'gender',
    'marital_status',
    'phone',
    'mobile',
    
    // Endereço
    'address_zip_code',
    'address_street',
    'address_number',
    'address_city',
    'address_state',
    
    // Contrato
    'admission_date',
    'resignation_date',
    'role_id',
    'department',
    'contract_type',
    'salary',
    'work_shift_id',
    
    // Bancários
    'bank_name',
    'bank_agency',
    'bank_account',
    'pix_key',
    
    // Documentos
    'ctps_number',
    'ctps_series',
    'pis_pasep',
    
    'status',
    'is_active',
];
```

### 3.2 Relacionamentos
- `role()`: BelongsTo Role (Cargo)
- `workShift()`: BelongsTo WorkShift (Turno)
- `timeRecords()`: HasMany TimeRecord (Registros de ponto)
- `payrolls()`: HasMany Payroll (Folhas de pagamento)

---

## 4. Integrações

### 4.1 Módulo de RH
- Folha de Pagamento: Calcula salários
- Ponto: Registros de jornada
- Holerite: Geração de contracheques

### 4.2 Módulo Financeiro
- Contas a Pagar: Pagamento de salários

---

## 5. Exemplos

```php
Employees::create([
    'name' => 'João Silva',
    'cpf' => '123.456.789-00',
    'email' => 'joao@empresa.com',
    'admission_date' => '2024-01-15',
    'role_id' => $cargo->id,
    'salary' => 3500.00,
    'contract_type' => 'clt',
    'status' => 'active',
]);
```

---

**Caminho**: `app/Models/Employees.php`, `app/Livewire/Cadastro/Funcionarios/`

