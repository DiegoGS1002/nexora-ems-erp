# Módulo de Jornada de Trabalho e Ponto

## 1. Visão Geral

O módulo de Jornada gerencia turnos de trabalho, horários e registros de ponto dos funcionários, calculando horas trabalhadas, extras, banco de horas e ausências.

### Funcionalidades Principais
- Gestão de turnos de trabalho
- Registro de ponto eletrônico
- Cálculo automático de horas
- Horas extras (50%, 100%)
- Banco de horas
- Controle de ausências
- Relatórios de frequência

---

## 2. Regras de Negócio

### 2.1 Turnos (WorkShift)
Define horário padrão de trabalho:
```
Turno Comercial:
- Entrada: 08:00
- Saída Almoço: 12:00
- Retorno Almoço: 13:00
- Saída: 17:00
- Total: 8h
```

### 2.2 Tipos de Registro
- **Entrada**: Chegada ao trabalho
- **Saída Almoço**: Início do intervalo
- **Retorno Almoço**: Fim do intervalo
- **Saída**: Fim do expediente

### 2.3 Horas Extras
- **50%**: Dias úteis após jornada normal
- **100%**: Domingos e feriados

### 2.4 Tolerância
- Atraso até 10 min: não desconta
- Acima de 10 min: desconta proporcional

---

## 3. Estrutura Técnica

### 3.1 Model: `WorkShift` (Turno)

```php
protected $fillable = [
    'name',
    'start_time',      // 08:00
    'lunch_start',     // 12:00
    'lunch_end',       // 13:00
    'end_time',        // 17:00
    'total_hours',     // 8
    'tolerance_minutes', // 10
    'is_active',
];
```

### 3.2 Model: `TimeRecord` (Registro de Ponto)

```php
protected $fillable = [
    'employee_id',
    'work_shift_id',
    'date',
    'entry_time',
    'lunch_start_time',
    'lunch_end_time',
    'exit_time',
    'total_worked_hours',
    'extra_hours',
    'missing_hours',
    'status', // present, absent, late, leave
    'notes',
];
```

### 3.3 Service: `JornadaService`

**Métodos**:
```php
calculateWorkedHours(TimeRecord $record): float
    // Calcula horas trabalhadas

calculateExtraHours(TimeRecord $record, WorkShift $shift): float
    // Calcula horas extras

markAbsence(Employee $employee, Carbon $date, string $reason): void
    // Marca falta

getBankHours(Employee $employee): float
    // Retorna saldo banco de horas
```

---

## 4. Fluxos

### 4.1 Registro de Ponto
```
1. Funcionário bate ponto (entrada)
2. Sistema registra entry_time
3. Almoço (saída): lunch_start_time
4. Retorno: lunch_end_time
5. Saída: exit_time
6. Ao final:
   - Calcula total_worked_hours
   - Compara com turno esperado
   - Calcula extra_hours ou missing_hours
```

### 4.2 Cálculo de Horas
```
Entrada: 08:05 (5 min atraso, dentro da tolerância)
Saída Almoço: 12:00
Retorno: 13:00
Saída: 18:00 (1h extra)

Horas trabalhadas: 8h
Horas extras (50%): 1h
```

---

## 5. Exemplos

```php
// Criar turno
$shift = WorkShift::create([
    'name' => 'Comercial',
    'start_time' => '08:00',
    'lunch_start' => '12:00',
    'lunch_end' => '13:00',
    'end_time' => '17:00',
    'total_hours' => 8,
    'tolerance_minutes' => 10,
]);

// Registrar ponto
$record = TimeRecord::create([
    'employee_id' => $employee->id,
    'work_shift_id' => $shift->id,
    'date' => today(),
    'entry_time' => '08:05',
    'exit_time' => '18:00',
]);

// Calcular horas
$service = app(JornadaService::class);
$workedHours = $service->calculateWorkedHours($record);
// Retorna: 8.92 (8h55min)

$extraHours = $service->calculateExtraHours($record, $shift);
// Retorna: 1.0 (1 hora extra)
```

---

**Caminho**: `app/Models/WorkShift.php`, `app/Models/TimeRecord.php`, `app/Services/JornadaService.php`, `app/Livewire/Rh/JornadaTrabalho.php`

