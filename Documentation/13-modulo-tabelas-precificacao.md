# Módulo de Tabelas de Precificação

## 1. Visão Geral

O módulo de Tabelas de Precificação permite criar múltiplas tabelas de preços para produtos, com regras diferenciadas por cliente, quantidade ou promoções. Inclui calculadora de markup para auxiliar na definição de preços.

### Funcionalidades Principais
- Múltiplas tabelas de preço
- Preços por produto
- Descontos progressivos por quantidade
- Vigência (data início e fim)
- Tabela padrão do sistema
- Vínculo com clientes
- Calculadora de markup
- Cálculo de margem de lucro

---

## 2. Regras de Negócio

### 2.1 Hierarquia de Preços
Ao vender, o sistema busca preço na seguinte ordem:
1. Preço específico na tabela do cliente
2. Preço na tabela padrão do sistema
3. Preço base do produto (sale_price)

### 2.2 Descontos Progressivos
Pode definir faixas de quantidade:
```
1-10 unidades: R$ 100,00
11-50 unidades: R$ 95,00 (5% desconto)
51+ unidades: R$ 90,00 (10% desconto)
```

### 2.3 Vigência
- `valid_from`: Data de início
- `valid_until`: Data de término
- Tabelas vencidas não são aplicadas

---

## 3. Estrutura Técnica

### 3.1 Model: `App\Models\PriceTable`

```php
protected $fillable = [
    'name',
    'code',
    'description',
    'valid_from',
    'valid_until',
    'is_default',
    'is_active',
];
```

### 3.2 Model: `PriceTableItem`

```php
protected $fillable = [
    'price_table_id',
    'product_id',
    'price',
    'min_quantity',
    'max_quantity',
    'discount_percent',
];
```

### 3.3 Service: `PricingService`

**Calculadora de Markup**:
```php
calculateFinalPrice(array $params): array
{
    // Parâmetros:
    // - cost_price
    // - icms_percent
    // - pis_percent
    // - cofins_percent
    // - ipi_percent
    // - operational_expenses_percent
    // - profit_margin_percent
    
    $totalTaxes = $icms + $pis + $cofins + $ipi;
    $totalExpenses = $operational_expenses;
    $totalPercentage = $totalTaxes + $totalExpenses + $profit_margin;
    
    $finalPrice = $cost_price / (1 - ($totalPercentage / 100));
    
    $markup = ($finalPrice / $cost_price) - 1;
    $margin = ($finalPrice - $cost_price) / $finalPrice;
    
    return [
        'final_price' => $finalPrice,
        'markup_percentage' => $markup * 100,
        'margin_percentage' => $margin * 100,
    ];
}
```

### 3.4 Livewire: `App\Livewire\Vendas\TabelasPrecificacao`

**Funcionalidades**:
- CRUD de tabelas
- Gestão de itens por tabela
- Calculadora de markup integrada
- Duplicação de tabelas
- Ativação/desativação

---

## 4. Integrações

### 4.1 Clientes
Cliente pode ter uma tabela padrão vinculada (`price_table_id`).

### 4.2 Pedidos de Venda
Ao criar pedido, sistema busca preço da tabela do cliente.

---

## 5. Exemplos

### 5.1 Criar Tabela
```php
$table = PriceTable::create([
    'name' => 'Tabela Premium',
    'code' => 'PREMIUM',
    'valid_from' => '2026-01-01',
    'valid_until' => '2026-12-31',
    'is_default' => false,
    'is_active' => true,
]);
```

### 5.2 Adicionar Preços
```php
// Preço único
$table->items()->create([
    'product_id' => $product->id,
    'price' => 95.00,
]);

// Preço progressivo
$table->items()->create([
    'product_id' => $product->id,
    'price' => 100.00,
    'min_quantity' => 1,
    'max_quantity' => 10,
]);

$table->items()->create([
    'product_id' => $product->id,
    'price' => 95.00,
    'min_quantity' => 11,
    'max_quantity' => null, // sem limite
]);
```

### 5.3 Calcular Markup
```php
$service = app(PricingService::class);

$result = $service->calculateFinalPrice([
    'cost_price' => 100.00,
    'icms_percent' => 18.00,
    'pis_percent' => 1.65,
    'cofins_percent' => 7.60,
    'operational_expenses_percent' => 10.00,
    'profit_margin_percent' => 15.00,
]);

// Resultado:
// final_price: 198.41
// markup_percentage: 98.41%
// margin_percentage: 49.59%
```

---

## 6. Calculadora de Markup

### 6.1 Fórmula

```
Preço Final = Custo / (1 - (% Total / 100))

Onde % Total = Impostos + Despesas Operacionais + Margem de Lucro

Markup = (Preço Final / Custo) - 1
Margem = (Preço Final - Custo) / Preço Final
```

### 6.2 Exemplo Visual

```
Custo: R$ 100,00
+ ICMS: 18%
+ PIS/COFINS: 9,25%
+ Despesas: 10%
+ Lucro: 15%
= Total: 52,25%

Preço Final = 100 / (1 - 0,5225) = R$ 209,42

Markup: 109,42%
Margem: 52,25%
```

---

**Caminho**: `app/Models/PriceTable.php`, `app/Services/PricingService.php`, `app/Livewire/Vendas/TabelasPrecificacao.php`

