# Módulo de Funções/Cargos

## 1. Visão Geral

Gerencia os cargos da empresa, definindo hierarquia, salários e permissões por função.

### Funcionalidades
- Cadastro de cargos
- Hierarquia organizacional
- Faixa salarial
- Descrição de atividades
- Vínculo com funcionários

---

## 2. Estrutura

### Model: `Role`
```php
protected $fillable = [
    'name',
    'description',
    'department',
    'salary_min',
    'salary_max',
    'level', // junior, pleno, senior
    'is_active',
];
```

### Relacionamentos
- `employees()`: HasMany Employees

---

## 3. Exemplos

```php
Role::create([
    'name' => 'Analista de TI',
    'description' => 'Desenvolvimento de sistemas',
    'department' => 'Tecnologia',
    'salary_min' => 3500.00,
    'salary_max' => 6000.00,
    'level' => 'pleno',
]);
```

---

**Caminho**: `app/Models/Role.php`, `app/Livewire/Cadastro/Funcoes/`

