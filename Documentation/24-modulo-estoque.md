# Módulo de Estoque e Movimentações

## 1. Visão Geral

O módulo de Estoque gerencia o inventário de produtos, registrando todas as movimentações (entradas, saídas, ajustes e transferências). É o núcleo operacional que integra Compras, Vendas e Produção, garantindo rastreabilidade completa.

### Funcionalidades Principais
- Controle de estoque em tempo real
- Movimentações detalhadas por produto
- Tipos: Entrada, Saída, Ajuste, Transferência
- Rastreabilidade completa (origem, usuário, data)
- Alertas de estoque baixo
- Custo médio ponderado (opcional)
- Inventário e contagem
- Relatórios de movimentação

---

## 2. Regras de Negócio

### 2.1 Tipos de Movimentação

#### Input (Entrada)
- **Origem**: Compras, Produção, Devolução de Cliente
- **Efeito**: Incrementa `Product.stock`
- **Exemplo**: Recebimento de 100 unidades do fornecedor

#### Output (Saída)
- **Origem**: Vendas, Produção (consumo), Perda
- **Efeito**: Decrementa `Product.stock`
- **Exemplo**: Separação de 50 unidades para pedido de venda

#### Adjustment (Ajuste)
- **Origem**: Inventário, Correção de Erro
- **Efeito**: Define `Product.stock` para valor exato
- **Exemplo**: Após inventário físico, ajustar de 95 para 100

#### Transfer (Transferência)
- **Origem**: Transferência entre locais/depósitos
- **Efeito**: Decrementa origem, incrementa destino
- **Exemplo**: Transferir 20 unidades do depósito A para B

### 2.2 Controle de Estoque Mínimo

```php
if ($product->stock < $product->stock_min) {
    // Cria notificação de estoque baixo
    Notification::send(
        User::where('is_admin', true)->get(),
        new LowStockNotification($product)
    );
}
```

### 2.3 Validação de Estoque Negativo

Por padrão, o sistema **permite** estoque negativo (útil para vendas antecipadas).

Para bloquear:
```php
// No arquivo de configuração config/stock.php
'allow_negative_stock' => false,

// Na movimentação
if (!config('stock.allow_negative_stock') && $product->stock - $quantity < 0) {
    throw new Exception('Estoque insuficiente');
}
```

### 2.4 Custo Médio Ponderado (Opcional)

Quando ativado, calcula custo médio a cada entrada:
```php
// Entrada de 50 unidades a R$ 10,00 (estoque: 100 a R$ 8,00)
$currentValue = $product->stock * $product->cost_price; // 100 * 8 = 800
$newValue = $quantity * $unit_cost;                     // 50 * 10 = 500
$totalValue = $currentValue + $newValue;                // 1300
$totalQuantity = $product->stock + $quantity;           // 150

$product->cost_price = $totalValue / $totalQuantity;    // 1300 / 150 = 8,67
```

### 2.5 Rastreabilidade

Toda movimentação registra:
- Produto movimentado
- Quantidade
- Tipo de movimentação
- Origem (descrição ou vínculo)
- Usuário responsável
- Data e hora
- Custo unitário (opcional)

---

## 3. Estrutura Técnica

### 3.1 Model: `App\Models\StockMovement`

**Caminho**: `app/Models/StockMovement.php`

#### Propriedades
```php
protected $fillable = [
    'product_id',         // UUID do produto
    'user_id',            // ID do usuário que fez a movimentação
    'quantity',           // Quantidade movimentada (sempre positiva)
    'type',               // input, output, adjustment, transfer
    'origin',             // Descrição da origem
    'origin_type',        // Tipo do modelo origem (opcional)
    'origin_id',          // ID do registro origem (opcional)
    'unit_cost',          // Custo unitário (opcional)
    'notes',              // Observações
    'reference_code',     // Código de referência (NFe, OP, etc.)
    'location_from',      // Local de origem (transferência)
    'location_to',        // Local de destino (transferência)
];
```

#### Casts
```php
protected $casts = [
    'quantity'  => 'decimal:3',
    'unit_cost' => 'decimal:2',
];
```

#### Relacionamentos
- `product()`: BelongsTo Product
- `user()`: BelongsTo User
- `origin()`: MorphTo (polimórfico: SalesOrder, PurchaseOrder, ProductionOrder)

#### Métodos Principais
```php
applyToStock(): void
    // Aplica movimentação ao estoque do produto
    // Input: incrementa
    // Output: decrementa
    // Adjustment: define valor exato
    
revert(): void
    // Reverte movimentação (exclusão)
    // Input: decrementa
    // Output: incrementa
```

### 3.2 Model: `App\Models\Product` (Relativo ao Estoque)

**Campos de Estoque**:
```php
'stock'       => 'decimal:3',  // Estoque atual
'stock_min'   => 'decimal:3',  // Estoque mínimo (alerta)
'stock_max'   => 'decimal:3',  // Estoque máximo (referência)
'cost_price'  => 'decimal:2',  // Custo médio
```

#### Métodos de Estoque
```php
hasStock(float $quantity = 1): bool
    // Verifica se tem estoque disponível

isLowStock(): bool
    // Verifica se stock < stock_min

incrementStock(float $quantity): void
    // Incrementa estoque

decrementStock(float $quantity): void
    // Decrementa estoque

adjustStock(float $newQuantity): void
    // Ajusta para quantidade exata
```

### 3.3 Livewire Component: `App\Livewire\Estoque\Movimentacao`

**Caminho**: `app/Livewire/Estoque/Movimentacao.php`

**Funcionalidades**:
- Listagem de movimentações com filtros:
  - Por produto
  - Por tipo (entrada, saída, ajuste, transferência)
  - Por período (data inicial e final)
  - Por usuário
  - Por origem
- Cards com KPIs:
  - Total de movimentações
  - Entradas (quantidade)
  - Saídas (quantidade)
  - Ajustes realizados
- Modal de nova movimentação:
  1. Selecionar produto
  2. Escolher tipo
  3. Informar quantidade
  4. Origem/descrição
  5. Custo unitário (se entrada)
  6. Observações
- Histórico completo por produto
- Exportação para Excel/PDF

---

## 4. Rotas

### 4.1 Rotas Web

**Arquivo**: `routes/estoque.php`

| Método | URI | Nome | Descrição |
|--------|-----|------|-----------|
| GET | `/stock` | `stock.index` | Painel de estoque |
| POST | `/stock/movement` | `stock.movement` | Registrar movimentação |

### 4.2 Rotas API

**Arquivo**: `routes/api.php`

| Método | URI | Descrição |
|--------|-----|-----------|
| GET | `/api/stock-movements` | Lista movimentações |
| POST | `/api/stock-movements` | Cria movimentação |
| GET | `/api/stock-movements/{id}` | Detalha movimentação |
| DELETE | `/api/stock-movements/{id}` | Remove movimentação (reverte) |
| GET | `/api/stock-movements/product/{id}` | Histórico do produto |
| GET | `/api/stock-movements/low-stock` | Produtos com estoque baixo |

---

## 5. Fluxos de Dados

### 5.1 Entrada de Estoque (Compra)

```
1. Pedido de Compra recebe mercadoria
2. Status muda para "RecebidoTotal"
3. Observer detecta mudança
4. Para cada item do pedido:
   a. Cria StockMovement:
      - type: input
      - quantity: item.quantity
      - origin: "Pedido de Compra #1234"
      - unit_cost: item.cost_price
   b. Incrementa Product.stock
   c. Se configurado, atualiza cost_price (custo médio)
5. Registra log de auditoria
6. Verifica se resolveu estoque baixo (notifica)
```

### 5.2 Saída de Estoque (Venda)

```
1. Pedido de Venda muda para "EmSeparacao"
2. Observer detecta mudança
3. Para cada item do pedido:
   a. Verifica disponibilidade:
      - Se product.stock < item.quantity: lança Exception
   b. Cria StockMovement:
      - type: output
      - quantity: item.quantity
      - origin: "Pedido de Venda #5678"
   c. Decrementa Product.stock
4. Se product.stock < product.stock_min:
   - Cria notificação de estoque baixo
5. Registra log
```

### 5.3 Ajuste de Estoque (Inventário)

```
1. Realiza contagem física
2. Identifica diferenças (físico vs. sistema)
3. Para cada produto com diferença:
   a. Usuário acessa modal de ajuste
   b. Informa:
      - Quantidade física contada
      - Motivo do ajuste
   c. Sistema calcula diferença:
      diferenca = quantidade_fisica - product.stock
   d. Cria StockMovement:
      - type: adjustment
      - quantity: quantidade_fisica (não a diferença)
      - origin: "Inventário - {motivo}"
   e. Define Product.stock = quantidade_fisica
4. Gera relatório de ajustes
```

### 5.4 Transferência Entre Depósitos

```
1. Usuário seleciona produto
2. Informa:
   - Quantidade a transferir
   - Depósito origem (location_from)
   - Depósito destino (location_to)
3. Sistema cria 2 movimentações:
   
   Movimentação 1 (Saída do Origem):
   - type: transfer
   - quantity: X
   - location_from: "Depósito A"
   - location_to: "Depósito B"
   - origin: "Transferência para Depósito B"
   
   Movimentação 2 (Entrada no Destino):
   - type: transfer
   - quantity: X
   - location_from: "Depósito A"
   - location_to: "Depósito B"
   - origin: "Transferência de Depósito A"
   
4. Atualiza saldo por local (se sistema multi-depósito)
```

---

## 6. Integrações com Outros Módulos

### 6.1 Módulo de Compras
**Pedido de Compra → Entrada de Estoque**
- Status `RecebidoTotal` cria movimentações tipo `input`
- Atualiza custo médio do produto
- Quantidade recebida é somada ao estoque

### 6.2 Módulo de Vendas
**Pedido de Venda → Saída de Estoque**
- Status `EmSeparacao` cria movimentações tipo `output`
- Valida disponibilidade antes de separar
- Decrementa estoque imediatamente

### 6.3 Módulo de Produção
**Ordem de Produção → Movimentações Duplas**
- Ao concluir:
  - Cria `output` para cada insumo (BOM)
  - Cria `input` para produto acabado
- Rastreia consumo de matéria-prima

### 6.4 Módulo Fiscal
**NF-e de Entrada → Conferência de Estoque**
- XML da NF-e pode ser comparado com recebimento
- Divergências geram ajustes

---

## 7. Exemplos de Código

### 7.1 Criar Entrada de Estoque
```php
use App\Models\StockMovement;
use App\Models\Product;

$product = Product::find($productId);

// Cria movimentação
$movement = StockMovement::create([
    'product_id' => $product->id,
    'user_id' => auth()->id(),
    'quantity' => 100,
    'type' => 'input',
    'origin' => 'Pedido de Compra #1234',
    'origin_type' => 'App\Models\PurchaseOrder',
    'origin_id' => $purchaseOrderId,
    'unit_cost' => 50.00,
]);

// Atualiza estoque
$product->increment('stock', 100);

// Ou usando método do model
$movement->applyToStock();
```

### 7.2 Criar Saída de Estoque
```php
$product = Product::find($productId);

// Valida disponibilidade
if (!$product->hasStock(50)) {
    throw new Exception('Estoque insuficiente');
}

// Cria movimentação
StockMovement::create([
    'product_id' => $product->id,
    'user_id' => auth()->id(),
    'quantity' => 50,
    'type' => 'output',
    'origin' => 'Pedido de Venda #5678',
]);

// Atualiza estoque
$product->decrement('stock', 50);

// Verifica se ficou baixo
if ($product->isLowStock()) {
    // Notifica responsável
}
```

### 7.3 Ajuste de Estoque
```php
$product = Product::find($productId);
$quantidadeFisica = 95; // Contagem física

StockMovement::create([
    'product_id' => $product->id,
    'user_id' => auth()->id(),
    'quantity' => $quantidadeFisica,
    'type' => 'adjustment',
    'origin' => 'Inventário - Contagem Física',
    'notes' => 'Diferença: -5 unidades (perda identificada)',
]);

// Define estoque exato
$product->update(['stock' => $quantidadeFisica]);
```

### 7.4 Consultar Histórico de Movimentações
```php
$movements = StockMovement::where('product_id', $productId)
    ->with('user')
    ->orderByDesc('created_at')
    ->paginate(20);

foreach ($movements as $movement) {
    echo "{$movement->type} - {$movement->quantity} - {$movement->origin}\n";
}
```

### 7.5 Produtos com Estoque Baixo
```php
$lowStockProducts = Product::where('is_active', true)
    ->whereColumn('stock', '<', 'stock_min')
    ->orderBy('stock')
    ->get();
```

### 7.6 Reverter Movimentação (Exclusão)
```php
$movement = StockMovement::find($id);

// Reverte no estoque
if ($movement->type === 'input') {
    $movement->product->decrement('stock', $movement->quantity);
} elseif ($movement->type === 'output') {
    $movement->product->increment('stock', $movement->quantity);
}

// Exclui movimentação
$movement->delete();
```

---

## 8. Relatórios e Análises

### 8.1 Relatório de Movimentações por Período
```php
$report = StockMovement::whereBetween('created_at', [$startDate, $endDate])
    ->selectRaw('type, SUM(quantity) as total_quantity, COUNT(*) as total_movements')
    ->groupBy('type')
    ->get();
```

### 8.2 Produtos Mais Movimentados
```php
$topProducts = StockMovement::selectRaw('product_id, SUM(quantity) as total_quantity')
    ->whereBetween('created_at', [$startDate, $endDate])
    ->groupBy('product_id')
    ->orderByDesc('total_quantity')
    ->take(10)
    ->with('product')
    ->get();
```

### 8.3 Valor Total em Estoque
```php
$totalStockValue = Product::where('is_active', true)
    ->selectRaw('SUM(stock * cost_price) as total_value')
    ->value('total_value');
```

---

## 9. KPIs e Métricas

### 9.1 KPIs do Estoque
```php
// Total de movimentações (mês)
$totalMovements = StockMovement::whereMonth('created_at', now()->month)->count();

// Entradas (quantidade)
$totalInput = StockMovement::where('type', 'input')
    ->whereMonth('created_at', now()->month)
    ->sum('quantity');

// Saídas (quantidade)
$totalOutput = StockMovement::where('type', 'output')
    ->whereMonth('created_at', now()->month)
    ->sum('quantity');

// Produtos com estoque baixo
$lowStockCount = Product::whereColumn('stock', '<', 'stock_min')->count();

// Giro de estoque (média de dias)
$averageTurnover = /* cálculo complexo baseado em entradas e saídas */;
```

---

## 10. Performance e Otimizações

### 10.1 Índices
- `product_id`: FK indexada
- `type`: Filtros rápidos
- `created_at`: Relatórios por período
- Composto `(product_id, created_at)`: Histórico do produto

### 10.2 Paginação
```php
// Sempre paginar históricos grandes
StockMovement::where('product_id', $id)
    ->paginate(PerPage::resolve());
```

### 10.3 Transações
```php
// Movimentações críticas devem usar transaction
DB::transaction(function () use ($product, $quantity) {
    StockMovement::create([...]);
    $product->decrement('stock', $quantity);
});
```

---

## 11. Testes

### 11.1 Testes de Feature
```php
test('entrada de estoque incrementa product.stock', function () {
    $product = Product::factory()->create(['stock' => 10]);
    
    StockMovement::create([
        'product_id' => $product->id,
        'user_id' => 1,
        'quantity' => 5,
        'type' => 'input',
        'origin' => 'Teste',
    ]);
    
    $product->increment('stock', 5);
    
    expect($product->fresh()->stock)->toBe(15);
});

test('saída de estoque decrementa product.stock', function () {
    $product = Product::factory()->create(['stock' => 10]);
    
    StockMovement::create([
        'product_id' => $product->id,
        'user_id' => 1,
        'quantity' => 3,
        'type' => 'output',
        'origin' => 'Teste',
    ]);
    
    $product->decrement('stock', 3);
    
    expect($product->fresh()->stock)->toBe(7);
});
```

---

## 12. Alertas e Notificações

### 12.1 Alerta de Estoque Baixo
```php
// Após qualquer saída, verificar
if ($product->stock < $product->stock_min) {
    Notification::send(
        User::whereIn('role', ['admin', 'buyer'])->get(),
        new LowStockNotification($product)
    );
}
```

### 12.2 Alerta de Estoque Negativo
```php
if ($product->stock < 0) {
    Log::warning('Estoque negativo', [
        'product_id' => $product->id,
        'stock' => $product->stock,
    ]);
}
```

---

## 13. Troubleshooting

### 13.1 Problemas Comuns
- **Estoque divergente**: Executar inventário e ajustar
- **Movimentação não aparece**: Verificar se `applyToStock()` foi chamado
- **Estoque negativo indesejado**: Ativar validação em `config/stock.php`

### 13.2 Comandos Úteis
```bash
php artisan tinker

# Produtos com estoque baixo
>>> Product::whereColumn('stock', '<', 'stock_min')->count();

# Movimentações do dia
>>> StockMovement::whereDate('created_at', today())->count();

# Histórico de um produto
>>> StockMovement::where('product_id', 'uuid')->latest()->take(10)->get();
```

---

**Caminho do Código**:
- `app/Models/StockMovement.php`
- `app/Models/Product.php` (campos de estoque)
- `app/Livewire/Estoque/Movimentacao.php`
- `routes/estoque.php`

