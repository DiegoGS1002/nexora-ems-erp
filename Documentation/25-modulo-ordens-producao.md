# Módulo de Ordens de Produção

## 1. Visão Geral

Gerencia o processo produtivo, controlando ordens de fabricação, consumo de insumos (BOM - Bill of Materials) e produção de produtos acabados.

### Funcionalidades
- Criação de ordens de produção
- BOM (estrutura de produtos)
- Consumo automático de insumos
- Produção de acabados
- Status workflow
- Custos de produção

---

## 2. Estrutura

### Model: `ProductionOrder`
```php
protected $fillable = [
    'order_number',
    'product_id', // Produto a ser fabricado
    'quantity',
    'status', // pending, in_progress, completed, canceled
    'start_date',
    'end_date',
    'expected_completion_date',
    'notes',
];
```

### BOM (Bill of Materials)
```php
ProductBom::create([
    'product_id' => $produtoAcabado->id,
    'component_id' => $materiaPrima->id,
    'quantity' => 2.5, // Quantidade necessária por unidade
]);
```

### Fluxo
```
1. Criar OP para fabricar 100 unidades do Produto X
2. Sistema calcula insumos necessários (BOM):
   - Matéria A: 250 unidades
   - Matéria B: 100 unidades
3. Ao iniciar: status = in_progress
4. Ao concluir:
   - Cria StockMovement output para insumos (baixa)
   - Cria StockMovement input para produto acabado (entrada)
   - Status = completed
```

---

**Caminho**: `app/Models/ProductionOrder.php`, `app/Livewire/Producao/OrdensProducao.php`

