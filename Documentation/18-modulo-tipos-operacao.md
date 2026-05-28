# Módulo de Tipos de Operação Fiscal

## 1. Visão Geral

Define os tipos de operações fiscais (CFOP, natureza) para NF-e, automatizando configurações tributárias por tipo de transação.

### Funcionalidades
- Cadastro de tipos de operação
- CFOP padrão
- Configuração de impostos
- CST/CSOSN automáticos
- Mensagens fiscais padrão

---

## 2. Estrutura

### Model: `TipoOperacao`
```php
protected $fillable = [
    'code',
    'name',
    'description',
    'cfop_within_state', // CFOP dentro do estado
    'cfop_outside_state', // CFOP fora do estado
    'nature', // venda, devolucao, bonificacao
    'tax_calculation', // automatic, manual
    'default_icms_cst',
    'default_pis_cst',
    'default_cofins_cst',
    'fiscal_message',
    'is_active',
];
```

---

## 3. Uso

Ao criar NF-e, seleciona tipo de operação e sistema aplica configurações automaticamente:

```php
$tipoOp = TipoOperacao::find($tipoOperacaoId);

$nfe->cfop = ($client->state === $company->state) 
    ? $tipoOp->cfop_within_state 
    : $tipoOp->cfop_outside_state;

$nfe->items->each(function($item) use ($tipoOp) {
    $item->icms_cst = $tipoOp->default_icms_cst;
});
```

---

**Caminho**: `app/Models/TipoOperacao.php`, `app/Livewire/Fiscal/TiposOperacao.php`

