# Módulo de Produtos

## 1. Visão Geral

O módulo de Produtos é o coração do sistema ERP, gerenciando todo o catálogo de itens comercializados pela empresa. Este módulo suporta produtos acabados, matérias-primas, serviços e ativos, com controle completo de estoque, precificação e informações fiscais.

### Funcionalidades Principais
- Cadastro completo de produtos (acabados, matérias-primas, serviços)
- Controle de estoque (quantidade atual, mínima, máxima)
- Gestão de preços (custo e venda)
- Informações fiscais (NCM, CFOP, grupo tributário)
- Categorização e unidades de medida
- Vínculo com fornecedores
- Imagens de produtos
- Soft delete (exclusão lógica)

---

## 2. Regras de Negócio

### 2.1 Tipos de Produto
- **Produto Acabado** (`product`): Item final para venda
- **Matéria-Prima** (`raw_material`): Insumo para produção
- **Serviço** (`service`): Não movimenta estoque físico
- **Ativo** (`asset`): Equipamento ou bem da empresa

### 2.2 Natureza do Produto
- **Para Revenda** (`revenda`): Comprado e revendido sem modificação
- **Para Industrialização** (`industrializacao`): Usado na produção
- **Uso e Consumo** (`uso_consumo`): Material de escritório, limpeza, etc.
- **Ativo Imobilizado** (`ativo_imobilizado`): Equipamentos, veículos, etc.

### 2.3 Código do Produto
- Gerado automaticamente no formato `PROD-XXXXXX` (6 dígitos)
- Pode ser substituído por código manual
- Deve ser único no sistema
- Sequencial baseado no total de produtos (incluindo excluídos)

### 2.4 Controle de Estoque
- **Estoque Atual**: Quantidade disponível
- **Estoque Mínimo**: Alerta quando atingido
- **Estoque Máximo**: Referência para compras
- Movimentações registradas em `StockMovement`

### 2.5 Precificação
- **Preço de Custo**: Valor de aquisição/produção
- **Preço de Venda**: Valor base de comercialização
- Pode ser sobrescrito por Tabelas de Precificação
- Cálculo de markup e margem via `PricingService`

### 2.6 Informações Fiscais
- **NCM** (Nomenclatura Comum do Mercosul): Obrigatório para NF-e
- **CFOP de Saída**: Código fiscal de operação de venda
- **CFOP de Entrada**: Código fiscal de operação de compra
- **Grupo Tributário**: Define ICMS, IPI, PIS/COFINS
- **EAN/GTIN**: Código de barras (opcional)

---

## 3. Estrutura Técnica

### 3.1 Model: `App\Models\Product`

**Caminho**: `app/Models/Product.php`

#### Propriedades
```php
protected $fillable = [
    'product_code',           // Código único (PROD-XXXXXX)
    'name',                   // Nome do produto
    'ean',                    // Código de barras EAN/GTIN
    'ncm',                    // NCM (8 dígitos)
    'cfop_saida',             // CFOP de saída (ex: 5102)
    'cfop_entrada',           // CFOP de entrada (ex: 1102)
    'grupo_tributario_id',    // FK para GrupoTributario
    'unit_of_measure_id',     // FK para UnitOfMeasure
    'product_category_id',    // FK para ProductCategory
    'description',            // Descrição detalhada
    'short_description',      // Descrição curta
    'brand',                  // Marca
    'product_type',           // Enum: TipoProduto
    'nature',                 // Enum: NaturezaProduto
    'product_line',           // Linha de produto
    'unit_of_measure',        // Unidade (legado, usar unit_of_measure_id)
    'category',               // Categoria (legado, usar product_category_id)
    'sale_price',             // Preço de venda
    'cost_price',             // Preço de custo
    'stock',                  // Estoque atual
    'stock_min',              // Estoque mínimo
    'expiration_date',        // Data de validade
    'weight_net',             // Peso líquido (kg)
    'weight_gross',           // Peso bruto (kg)
    'height',                 // Altura (cm)
    'width',                  // Largura (cm)
    'depth',                  // Profundidade (cm)
    'full_description',       // Descrição completa (HTML)
    'is_active',              // Ativo/Inativo
    'highlights',             // Destaques (array)
    'tags',                   // Tags de busca (array)
    'image',                  // Caminho da imagem
];
```

#### Casts
```php
protected $casts = [
    'sale_price'      => 'decimal:2',
    'cost_price'      => 'decimal:2',
    'weight_net'      => 'decimal:3',
    'weight_gross'    => 'decimal:3',
    'height'          => 'decimal:2',
    'width'           => 'decimal:2',
    'depth'           => 'decimal:2',
    'expiration_date' => 'date',
    'is_active'       => 'boolean',
    'highlights'      => 'array',
    'tags'            => 'array',
    'product_type'    => TipoProduto::class,
    'nature'          => NaturezaProduto::class,
];
```

#### Relacionamentos
- `suppliers()`: BelongsToMany Supplier (tabela pivot `product_supplier`)
- `grupoTributario()`: BelongsTo GrupoTributario
- `unitOfMeasure()`: BelongsTo UnitOfMeasure
- `productCategory()`: BelongsTo ProductCategory

#### Métodos
- `getImageUrlAttribute()`: Retorna URL completa da imagem ou placeholder

### 3.2 Enums

#### `App\Enums\TipoProduto`
```php
enum TipoProduto: string
{
    case Product = 'product';           // Produto acabado
    case RawMaterial = 'raw_material';  // Matéria-prima
    case Service = 'service';           // Serviço
    case Asset = 'asset';               // Ativo
}
```

#### `App\Enums\NaturezaProduto`
```php
enum NaturezaProduto: string
{
    case Revenda = 'revenda';
    case Industrializacao = 'industrializacao';
    case UsoConsumo = 'uso_consumo';
    case AtivoImobilizado = 'ativo_imobilizado';
}
```

### 3.3 Livewire Components

#### Index: `App\Livewire\Cadastro\Produtos\Index`
**Caminho**: `app/Livewire/Cadastro/Produtos/Index.php`

**Funcionalidades**:
- Listagem paginada de produtos
- Busca por código, nome, EAN ou NCM
- Filtro por categoria, tipo e status (ativo/inativo)
- Ordenação por múltiplas colunas
- Ações: visualizar, editar, excluir (soft delete)
- Indicador visual de estoque baixo (vermelho se < stock_min)

#### Form: `App\Livewire\Cadastro\Produtos\Form`
**Caminho**: `app/Livewire/Cadastro/Produtos/Form.php`

**Funcionalidades**:
- Formulário com 5 abas: Geral, Fiscal, Estoque, Dimensões, Descrição
- Upload de imagem
- Seletor de categoria e unidade de medida
- Calculadora de preço de venda (markup)
- Vínculo com fornecedores
- Auto-completar NCM via consulta

### 3.4 Controller: `App\Http\Controllers\ProductController`

**Caminho**: `app/Http/Controllers/ProductController.php`

#### Métodos
- `print()`: Gera relatório PDF de produtos

---

## 4. Rotas

### 4.1 Rotas Web

**Arquivo**: `routes/cadastro.php`

| Método | URI | Nome | Descrição |
|--------|-----|------|-----------|
| GET | `/products` | `products.index` | Listagem de produtos |
| GET | `/products/create` | `products.create` | Formulário de criação |
| GET | `/products/{product}/edit` | `products.edit` | Formulário de edição |
| GET | `/products/print` | `products.print` | Relatório PDF |
| GET | `/products/{product}/suppliers` | `products.suppliers.index` | Fornecedores do produto |
| POST | `/products/{product}/suppliers` | `products.suppliers.store` | Vincular fornecedor |
| DELETE | `/products/{product}/suppliers/{supplier}` | `products.suppliers.destroy` | Desvincular fornecedor |

### 4.2 Rotas API

**Arquivo**: `routes/api.php`

| Método | URI | Descrição |
|--------|-----|-----------|
| GET | `/api/products` | Lista produtos |
| POST | `/api/products` | Cria produto |
| GET | `/api/products/{id}` | Detalha produto |
| PUT/PATCH | `/api/products/{id}` | Atualiza produto |
| DELETE | `/api/products/{id}` | Remove produto (soft delete) |

---

## 5. Fluxos de Dados

### 5.1 Criação de Produto

```
1. Usuário acessa /products/create
2. Preenche abas do formulário
3. Sistema gera product_code automaticamente (PROD-XXXXXX)
4. Sistema gera UUID como chave primária
5. Upload de imagem (se fornecida) → storage/app/public/products/
6. Salva produto no banco
7. Registra log de auditoria
8. Redireciona para listagem
```

### 5.2 Movimentação de Estoque

```
1. Venda/Compra/Produção é finalizada
2. Sistema cria StockMovement com:
   - product_id
   - type (input, output, adjustment, transfer)
   - quantity
   - user_id
3. Atualiza Product.stock:
   - Input: stock += quantity
   - Output: stock -= quantity
   - Adjustment: stock = quantity
4. Verifica se stock < stock_min → cria notificação
```

### 5.3 Cálculo de Preço com Markup

```
1. Usuário define cost_price
2. Aplica percentuais:
   - Impostos (ICMS, PIS, COFINS, IPI)
   - Despesas operacionais
   - Margem de lucro desejada
3. Sistema calcula sale_price:
   sale_price = cost_price / (1 - (soma_percentuais / 100))
4. Exibe markup e margem de lucro
```

---

## 6. Integrações com Outros Módulos

### 6.1 Módulo de Compras
- **Pedidos de Compra**: Itens são produtos cadastrados
- **Cotações**: Produtos vinculados a fornecedores
- **Recebimento**: Atualiza `stock` via `StockMovement`

### 6.2 Módulo de Vendas
- **Pedidos de Venda**: Itens são produtos
- **Tabelas de Precificação**: Definem preços por produto
- **Reserva de Estoque**: Desconta `stock` ao separar pedido

### 6.3 Módulo de Produção
- **Ordens de Produção**: Produtos acabados e matérias-primas
- **BOM (Bill of Materials)**: Estrutura de insumos por produto
- **Consumo**: Baixa estoque de matéria-prima
- **Produção**: Aumenta estoque de produto acabado

### 6.4 Módulo Fiscal
- **NF-e**: NCM, CFOP, grupo tributário obrigatórios
- **Cálculo de Impostos**: Usa `GrupoTributario` vinculado

### 6.5 Módulo de Estoque
- **Movimentações**: Registra entradas, saídas, ajustes
- **Inventário**: Compara estoque físico com sistema
- **Alertas**: Notifica quando estoque < stock_min

---

## 7. Tabelas Relacionadas

### 7.1 `product_supplier` (Pivot)
Relacionamento N:N entre produtos e fornecedores.

| Campo | Tipo | Descrição |
|-------|------|-----------|
| `id` | bigint | PK |
| `product_id` | uuid | FK para products |
| `supplier_id` | uuid | FK para suppliers |
| `supplier_code` | string | Código do produto no fornecedor |
| `cost_price` | decimal(10,2) | Preço de custo deste fornecedor |
| `delivery_time_days` | integer | Prazo de entrega (dias) |
| `is_preferred` | boolean | Fornecedor preferencial |

### 7.2 `stock_movements`
Registra todas as movimentações de estoque.

| Campo | Tipo | Descrição |
|-------|------|-----------|
| `id` | bigint | PK |
| `product_id` | uuid | FK para products |
| `user_id` | bigint | FK para users |
| `quantity` | decimal(10,3) | Quantidade movimentada |
| `type` | enum | input, output, adjustment, transfer |
| `origin` | string | Origem da movimentação |
| `unit_cost` | decimal(10,2) | Custo unitário (opcional) |
| `created_at` | timestamp | Data da movimentação |

---

## 8. Validações

### 8.1 Validações de Formulário
```php
'product_code' => 'required|string|max:50|unique:products,product_code',
'name' => 'required|string|max:255',
'product_type' => 'required|in:product,raw_material,service,asset',
'nature' => 'required|in:revenda,industrializacao,uso_consumo,ativo_imobilizado',
'sale_price' => 'nullable|numeric|min:0',
'cost_price' => 'nullable|numeric|min:0',
'stock' => 'nullable|numeric|min:0',
'stock_min' => 'nullable|numeric|min:0',
'ncm' => 'nullable|string|size:8',
'ean' => 'nullable|string|max:14',
'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
```

### 8.2 Validações de NCM
- Deve ter exatamente 8 dígitos
- Formato: 0000.00.00 (apenas números)
- Verificação via tabela TIPI (não implementado)

---

## 9. Exemplos de Código

### 9.1 Criar Produto
```php
use App\Models\Product;
use App\Enums\TipoProduto;
use App\Enums\NaturezaProduto;

$product = Product::create([
    'name' => 'Notebook Dell Inspiron 15',
    'product_type' => TipoProduto::Product,
    'nature' => NaturezaProduto::Revenda,
    'cost_price' => 2500.00,
    'sale_price' => 3500.00,
    'stock' => 10,
    'stock_min' => 2,
    'ncm' => '84713012',
    'cfop_saida' => '5102',
    'cfop_entrada' => '1102',
    'unit_of_measure_id' => $unidadeUn->id,
    'product_category_id' => $categoriaInformatica->id,
    'is_active' => true,
]);
```

### 9.2 Verificar Estoque Baixo
```php
$produtosBaixos = Product::where('is_active', true)
    ->whereColumn('stock', '<', 'stock_min')
    ->get();
```

### 9.3 Movimentar Estoque
```php
use App\Models\StockMovement;

// Entrada de estoque (compra)
StockMovement::create([
    'product_id' => $product->id,
    'user_id' => auth()->id(),
    'quantity' => 50,
    'type' => 'input',
    'origin' => 'Pedido de Compra #123',
    'unit_cost' => 2500.00,
]);

$product->increment('stock', 50);
```

### 9.4 Vincular Fornecedor
```php
$product->suppliers()->attach($supplier->id, [
    'supplier_code' => 'FORN-PROD-001',
    'cost_price' => 2400.00,
    'delivery_time_days' => 7,
    'is_preferred' => true,
]);
```

---

## 10. Performance e Otimizações

### 10.1 Índices de Banco de Dados
- `product_code`: Índice único
- `ean`: Índice para busca rápida
- `ncm`: Índice para filtros fiscais
- `is_active`: Índice para filtros
- `product_category_id`: FK indexada
- `stock`: Índice para consultas de estoque

### 10.2 Eager Loading
```php
Product::with([
    'productCategory',
    'unitOfMeasure',
    'grupoTributario',
    'suppliers'
])->get();
```

### 10.3 Soft Delete
Produtos excluídos são mantidos no banco com `deleted_at` preenchido:
```php
// Incluir excluídos
Product::withTrashed()->get();

// Apenas excluídos
Product::onlyTrashed()->get();

// Restaurar
$product->restore();
```

---

## 11. Testes

### 11.1 Testes de Feature
- `ProductCreationTest`: Criação com código automático
- `ProductStockTest`: Movimentações de estoque
- `ProductPricingTest`: Cálculo de markup
- `ProductSupplierTest`: Vínculo com fornecedores

### 11.2 Exemplo de Teste
```php
test('produto gera código automaticamente', function () {
    $product = Product::factory()->create([
        'product_code' => null,
    ]);
    
    expect($product->product_code)
        ->toStartWith('PROD-')
        ->toHaveLength(11);
});
```

---

## 12. Manutenção e Troubleshooting

### 12.1 Problemas Comuns
- **Estoque negativo**: Verificar movimentações incorretas
- **Código duplicado**: Sistema gera automaticamente, não alterar
- **Imagem não aparece**: Verificar `storage:link` e permissões

### 12.2 Comandos Úteis
```bash
# Produtos com estoque baixo
php artisan tinker
>>> Product::whereColumn('stock', '<', 'stock_min')->count();

# Produtos sem categoria
>>> Product::whereNull('product_category_id')->count();

# Reindexar produtos
>>> Product::query()->update(['updated_at' => now()]);
```

---

**Caminho do Código**: `app/Models/Product.php`, `app/Livewire/Cadastro/Produtos/`, `routes/cadastro.php`

