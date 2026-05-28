# Módulo de Batida de Ponto

## 1. Visão Geral

Complementa o módulo de Jornada, focando no registro diário de entrada/saída dos funcionários via web ou dispositivo biométrico.

### Funcionalidades
- Registro de ponto via web
- Integração com relógio ponto (opcional)
- Justificativa de ausências
- Atestados médicos
- Banco de horas
- Relatório de frequência

---

## 2. Estrutura

### Model: `TimeRecord` (já definido em Jornada)

### Ações
```php
// Bater ponto
TimeRecord::create([
    'employee_id' => auth()->user()->employee_id,
    'date' => today(),
    'entry_time' => now()->format('H:i'),
]);

// Saída
$record->update(['exit_time' => now()->format('H:i')]);
```

### Justificativas
```php
TimeRecordJustification::create([
    'time_record_id' => $record->id,
    'reason' => 'Atestado médico',
    'attachment' => 'storage/atestados/doc.pdf',
]);
```

---

**Caminho**: `app/Livewire/Rh/BatidaPonto.php`

