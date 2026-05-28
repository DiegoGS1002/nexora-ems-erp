# Módulo de Veículos

## 1. Visão Geral

Gerencia a frota de veículos da empresa para controle de entregas, manutenções e custos operacionais.

### Funcionalidades
- Cadastro de veículos (placa, modelo, ano)
- Controle de manutenções
- Registro de abastecimentos
- Quilometragem
- Vínculo com motoristas
- Custos operacionais

---

## 2. Estrutura

### Model: `Vehicle`
```php
protected $fillable = [
    'plate',
    'brand',
    'model',
    'year',
    'color',
    'chassis',
    'renavam',
    'km_current',
    'fuel_type',
    'status', // active, maintenance, inactive
];
```

### Relacionamentos
- `driver()`: BelongsTo Employee
- `maintenances()`: HasMany VehicleMaintenance
- `deliveries()`: HasMany SchedulingOfDeliveries

---

## 3. Exemplos

```php
Vehicle::create([
    'plate' => 'ABC-1234',
    'brand' => 'Fiat',
    'model' => 'Fiorino',
    'year' => 2020,
    'fuel_type' => 'flex',
    'status' => 'active',
]);
```

---

**Caminho**: `app/Models/Vehicle.php`, `app/Livewire/Cadastro/Veiculos/`

