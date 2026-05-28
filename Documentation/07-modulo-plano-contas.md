# Módulo de Plano de Contas

## 1. Visão Geral

O Plano de Contas é a estrutura hierárquica que organiza todas as contas contábeis da empresa, permitindo categorização de receitas, despesas, ativos e passivos para relatórios financeiros e contábeis.

### Funcionalidades Principais
- Estrutura hierárquica (até 5 níveis)
- 4 tipos de conta: Receita, Despesa, Ativo, Passivo
- Contas sintéticas (agrupadores) e analíticas (movimentadas)
- Numeração contábil (ex: 1.01.002)
- Importação de plano padrão
- Árvore visual interativa

---

## 2. Regras de Negócio

### 2.1 Tipos de Conta
- **Receita**: Entradas financeiras (vendas, serviços)
- **Despesa**: Saídas financeiras (aluguel, salários)
- **Ativo**: Bens e direitos (caixa, estoque, veículos)
- **Passivo**: Obrigações (fornecedores, empréstimos)

### 2.2 Contas Sintéticas vs Analíticas
- **Sintética**: Agrupa outras contas, não movimenta (`is_selectable = false`)
  - Ex: "1 - ATIVO"
- **Analítica**: Recebe lançamentos (`is_selectable = true`)
  - Ex: "1.01.001 - Caixa"

### 2.3 Hierarquia
```
1 - ATIVO (sintética)
├── 1.01 - Circulante (sintética)
│   ├── 1.01.001 - Caixa (analítica)
│   ├── 1.01.002 - Bancos (analítica)
│   └── 1.01.003 - Estoque (analítica)
└── 1.02 - Não Circulante (sintética)
    └── 1.02.001 - Veículos (analítica)
```

---

## 3. Estrutura Técnica

### 3.1 Model: `App\Models\PlanOfAccount`

```php
protected $fillable = [
    'parent_id',        // FK para hierarquia
    'code',             // 1.01.002
    'name',             // Nome da conta
    'type',             // receita, despesa, ativo, passivo
    'is_selectable',    // true = analítica, false = sintética
    'is_active',
    'description',
];
```

### 3.2 Métodos

```php
// Relacionamento pai-filho
public function parent(): BelongsTo
{
    return $this->belongsTo(PlanOfAccount::class, 'parent_id');
}

public function children(): HasMany
{
    return $this->hasMany(PlanOfAccount::class, 'parent_id');
}

// Verificações
public function isSynthetic(): bool
{
    return !$this->is_selectable;
}

public function hasChildren(): bool
{
    return $this->children()->exists();
}

// Impede exclusão se tiver filhos
protected static function booted()
{
    static::deleting(function ($account) {
        if ($account->hasChildren()) {
            throw new Exception('Conta possui subcontas');
        }
    });
}
```

### 3.3 Livewire: `App\Livewire\Financeiro\PlanoContas`

**Funcionalidades**:
- Tree view hierárquico
- Expandir/colapsar níveis
- Criar conta raiz ou subconta
- Editar/excluir (validações)
- Filtro por tipo
- Busca por código ou nome

---

## 4. Plano de Contas Padrão

### 4.1 Estrutura Básica
```
1 - ATIVO
  1.01 - Circulante
    1.01.001 - Caixa
    1.01.002 - Bancos
    1.01.003 - Estoque
  1.02 - Não Circulante
    1.02.001 - Imobilizado

2 - PASSIVO
  2.01 - Circulante
    2.01.001 - Fornecedores
    2.01.002 - Salários a Pagar
  2.02 - Não Circulante
    2.02.001 - Empréstimos

3 - RECEITAS
  3.01 - Receitas Operacionais
    3.01.001 - Vendas de Produtos
    3.01.002 - Prestação de Serviços

4 - DESPESAS
  4.01 - Despesas Operacionais
    4.01.001 - Aluguel
    4.01.002 - Salários
    4.01.003 - Fornecedores
  4.02 - Despesas Administrativas
    4.02.001 - Material de Escritório
```

### 4.2 Seeder
```php
// database/seeders/PlanoContasSeeder.php
PlanOfAccount::create([
    'code' => '1',
    'name' => 'ATIVO',
    'type' => 'ativo',
    'is_selectable' => false,
]);

PlanOfAccount::create([
    'parent_id' => $ativo->id,
    'code' => '1.01',
    'name' => 'Circulante',
    'type' => 'ativo',
    'is_selectable' => false,
]);

PlanOfAccount::create([
    'parent_id' => $circulante->id,
    'code' => '1.01.001',
    'name' => 'Caixa',
    'type' => 'ativo',
    'is_selectable' => true,
]);
```

---

## 5. Integrações

### 5.1 Contas a Pagar/Receber
Vincula cada conta financeira a uma conta contábil (`chart_of_account_id`).

### 5.2 Relatórios Contábeis
- Balanço Patrimonial
- DRE (Demonstração do Resultado do Exercício)
- Fluxo de Caixa

---

## 6. Exemplos

```php
// Criar conta sintética
$receitas = PlanOfAccount::create([
    'code' => '3',
    'name' => 'RECEITAS',
    'type' => 'receita',
    'is_selectable' => false,
]);

// Criar subconta analítica
$vendas = PlanOfAccount::create([
    'parent_id' => $receitas->id,
    'code' => '3.01.001',
    'name' => 'Vendas de Produtos',
    'type' => 'receita',
    'is_selectable' => true,
]);

// Buscar contas de despesa
$despesas = PlanOfAccount::where('type', 'despesa')
    ->where('is_selectable', true)
    ->get();

// Árvore completa
$tree = PlanOfAccount::whereNull('parent_id')
    ->with('children.children.children')
    ->get();
```

---

**Caminho**: `app/Models/PlanOfAccount.php`, `app/Livewire/Financeiro/PlanoContas.php`, `database/seeders/PlanoContasSeeder.php`

