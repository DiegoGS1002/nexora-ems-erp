# Módulo de Grupos Tributários

## 1. Visão Geral

Agrupa configurações tributárias (ICMS, PIS, COFINS, IPI) para facilitar aplicação em produtos, evitando configurar impostos item a item.

### Funcionalidades
- Grupos de tributação customizáveis
- Alíquotas por imposto
- CST/CSOSN padrão
- Vínculo com produtos
- Aplicação automática em NF-e

---

## 2. Estrutura

### Model: `GrupoTributario`
```php
protected $fillable = [
    'name',
    'description',
    
    // ICMS
    'icms_origin',
    'icms_cst',
    'icms_csosn',
    'icms_percent',
    'icms_reduction_base_percent',
    
    // IPI
    'ipi_cst',
    'ipi_percent',
    
    // PIS
    'pis_cst',
    'pis_percent',
    
    // COFINS
    'cofins_cst',
    'cofins_percent',
    
    'is_active',
];
```

---

## 3. Uso

### Vincular ao Produto
```php
$product->update([
    'grupo_tributario_id' => $grupoId,
]);
```

### Aplicar em NF-e
```php
$grupo = $product->grupoTributario;

$item->icms_cst = $grupo->icms_cst;
$item->icms_percent = $grupo->icms_percent;
$item->pis_percent = $grupo->pis_percent;
$item->cofins_percent = $grupo->cofins_percent;
```

---

## 4. Exemplos

### Grupo Simples Nacional
```php
GrupoTributario::create([
    'name' => 'Simples Nacional - Revenda',
    'icms_csosn' => '102', // Sem cálculo de ICMS
    'pis_cst' => '49',
    'pis_percent' => 0,
    'cofins_cst' => '49',
    'cofins_percent' => 0,
]);
```

### Grupo Lucro Real
```php
GrupoTributario::create([
    'name' => 'Lucro Real - Revenda',
    'icms_cst' => '00',
    'icms_percent' => 18.00,
    'pis_cst' => '01',
    'pis_percent' => 1.65,
    'cofins_cst' => '01',
    'cofins_percent' => 7.60,
]);
```

---

**Caminho**: `app/Models/GrupoTributario.php`, `app/Livewire/Fiscal/GruposTributarios.php`

