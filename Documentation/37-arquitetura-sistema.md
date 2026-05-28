# Arquitetura do Sistema Nexora ERP

## 1. Visão Geral

O Nexora ERP é um sistema monolítico modular baseado em Laravel 13, projetado para ser escalável, manutenível e testável. A arquitetura segue os princípios SOLID e utiliza padrões de projeto consolidados.

---

## 2. Stack Tecnológica Completa

### 2.1 Backend
- **Framework**: Laravel 13.8 (PHP 8.4+)
- **ORM**: Eloquent
- **Autenticação**: Laravel Sanctum
- **Filas**: Redis/Database
- **Cache**: Redis/File
- **Storage**: Local/S3
- **PDF**: DomPDF 3.1
- **Validação**: Laravel Validator

### 2.2 Frontend
- **Template Engine**: Blade
- **Componentes Reativos**: Livewire 4.3
- **CSS Framework**: Tailwind CSS 4.0 + Bootstrap 5.3.8
- **JavaScript**: Alpine.js 3.x
- **Gráficos**: ApexCharts (CDN)
- **Build Tool**: Vite 7.3

### 2.3 Admin Panel
- **Framework**: Filament 5.6.2
- **Cor Primária**: Amber
- **Dashboard**: Personalizado

### 2.4 Banco de Dados
- **Principal**: MySQL 8.x/9.x
- **Alternativo**: SQLite (desenvolvimento), PostgreSQL
- **Migrations**: Versionadas
- **Seeders**: Idempotentes

### 2.5 IA e Machine Learning
- **LLM Primário**: OpenAI GPT-4o-mini
- **LLM Secundário**: Google Gemini 1.5 Flash
- **Embeddings**: OpenAI text-embedding-3-small
- **RAG**: MySQL VECTOR (9.x) ou JSON (8.x)

### 2.6 Integrações Externas
- **BrasilAPI**: Consulta CEP, CNPJ
- **SEFAZ**: NF-e via nfephp-org
- **Correios**: Cálculo de frete (opcional)
- **Bancos**: Integração bancária (planejado)

---

## 3. Estrutura de Diretórios

```
nexora-ems-erp/
├── app/
│   ├── Ai/                      # Sistema de IA e RAG
│   │   ├── Tools/               # Ferramentas para function calling
│   │   ├── AgenteService.php
│   │   ├── RagService.php
│   │   └── PlaybookEngine.php
│   ├── Console/Commands/        # Comandos Artisan personalizados
│   ├── Enums/                   # Enumerações de domínio
│   ├── Filament/                # Recursos do Filament Admin
│   ├── Http/
│   │   ├── Controllers/         # Controllers organizados por domínio
│   │   │   ├── Api/             # API REST
│   │   │   └── Auth/            # Autenticação
│   │   └── Middleware/          # Middlewares customizados
│   ├── Livewire/                # Componentes Livewire (full-page e nested)
│   │   ├── Administracao/
│   │   ├── Cadastro/
│   │   ├── Compras/
│   │   ├── Dashboard/
│   │   ├── Estoque/
│   │   ├── Financeiro/
│   │   ├── Fiscal/
│   │   ├── Logistica/
│   │   ├── Producao/
│   │   ├── Rh/
│   │   ├── Suporte/
│   │   └── Vendas/
│   ├── Models/                  # Modelos Eloquent
│   ├── Observers/               # Observers de modelos
│   ├── Providers/               # Service Providers
│   ├── Services/                # Lógica de negócio
│   │   └── Dashboard/
│   ├── Support/                 # Classes auxiliares
│   └── Traits/                  # Traits reutilizáveis (Loggable, etc.)
├── bootstrap/
├── config/                      # Arquivos de configuração
│   ├── ai_contexts.php          # Contextos do assistente IA
│   ├── gemini.php
│   ├── nfe.php
│   └── openai.php
├── database/
│   ├── factories/               # Factories para testes
│   ├── migrations/              # Migrations versionadas
│   └── seeders/                 # Seeders idempotentes
├── Documentation/               # 📚 Esta documentação
├── docs/                        # Documentação técnica adicional
│   ├── design/
│   └── NF-e/
├── public/                      # Arquivos públicos
│   ├── build/                   # Assets compilados (Vite)
│   └── storage/                 # Link simbólico para storage/app/public
├── resources/
│   ├── css/
│   │   ├── app.css
│   │   ├── _base.css
│   │   ├── _components.css
│   │   ├── _layout.css
│   │   └── _tables.css
│   ├── js/
│   │   └── app.js
│   └── views/
│       ├── auth/
│       ├── layouts/
│       │   └── app.blade.php    # Layout principal
│       └── livewire/            # Views dos componentes Livewire
├── routes/
│   ├── web.php                  # Rota principal (inclui todos os módulos)
│   ├── api.php                  # API REST
│   ├── administracao.php
│   ├── cadastro.php
│   ├── compras.php
│   ├── estoque.php
│   ├── financeiro.php
│   ├── fiscal.php
│   ├── logistica.php
│   ├── perfil.php
│   ├── producao.php
│   ├── rh.php
│   └── vendas.php
├── storage/
│   ├── app/
│   │   ├── nfe/                 # XMLs e DANFEs
│   │   └── public/              # Arquivos públicos (avatars, produtos)
│   ├── framework/
│   └── logs/
├── tests/
│   ├── Feature/
│   └── Unit/
├── .env
├── composer.json
├── package.json
├── phpunit.xml
└── vite.config.js
```

---

## 4. Camadas da Aplicação

### 4.1 Camada de Apresentação (Frontend)
**Responsabilidades**:
- Renderização de views (Blade)
- Componentes reativos (Livewire)
- Interação do usuário (Alpine.js)
- Validação de formulários (client-side)

**Fluxo**:
```
Usuário → Livewire Component → Emite evento → Atualiza view
```

**Exemplo**:
```php
// app/Livewire/Cadastro/Clientes/Index.php
class Index extends Component
{
    public $search = '';
    public $perPage = 10;
    
    public function render()
    {
        $clients = Client::where('name', 'like', "%{$this->search}%")
            ->paginate($this->perPage);
        
        return view('livewire.cadastro.clientes.index', compact('clients'));
    }
}
```

### 4.2 Camada de Roteamento
**Responsabilidades**:
- Mapeamento de URLs para controllers/components
- Aplicação de middlewares
- Organização por módulos

**Padrões**:
- Rotas web em `routes/{modulo}.php`
- Rotas API em `routes/api.php`
- Nomes de rotas: `{modulo}.{acao}`

**Exemplo**:
```php
// routes/vendas.php
Route::get('/vendas/pedidos', PedidosVenda::class)->name('vendas.pedidos');
Route::get('/vendas/pedidos/novo', PedidoForm::class)->name('vendas.pedidos.novo');
```

### 4.3 Camada de Controle (Controllers)
**Responsabilidades**:
- Orquestração de requisições
- Validação de input
- Delegação para Services
- Formatação de respostas

**Princípios**:
- Controllers devem ser **slim** (enxutos)
- Lógica de negócio vai para Services
- Validações complexas vão para Form Requests

**Exemplo**:
```php
// app/Http/Controllers/ClientController.php
class ClientController extends Controller
{
    public function print(Request $request)
    {
        $clients = Client::where('situation', 'active')
            ->orderBy('name')
            ->get();
        
        $pdf = PDF::loadView('reports.clients', compact('clients'));
        
        return $pdf->download('clientes.pdf');
    }
}
```

### 4.4 Camada de Serviço (Services)
**Responsabilidades**:
- **Lógica de negócio complexa**
- Operações que envolvem múltiplos modelos
- Transações de banco de dados
- Integrações externas

**Quando criar um Service**:
- Operação envolve 3+ modelos
- Lógica reutilizada em múltiplos lugares
- Regras de negócio complexas

**Exemplo**:
```php
// app/Services/SalesOrderService.php
class SalesOrderService
{
    public function createOrder(array $data): SalesOrder
    {
        return DB::transaction(function () use ($data) {
            $this->validateOrder($data);
            $order = $this->createMainOrder($data);
            $this->addItems($order, $data['items']);
            $this->addPayment($order, $data['payment']);
            $order->calculateTotals();
            return $order;
        });
    }
}
```

### 4.5 Camada de Modelo (Models)
**Responsabilidades**:
- Mapeamento ORM (Eloquent)
- Relacionamentos entre entidades
- Casts e acessores
- Lógica de modelo simples (scopes, getters)

**Princípios**:
- Models devem ser **"fat"** em relacionamentos, **"thin"** em lógica
- Usar Observers para eventos complexos
- Traits para comportamentos reutilizáveis

**Exemplo**:
```php
// app/Models/Client.php
class Client extends Model
{
    use HasFactory, Loggable, SoftDeletes;
    
    protected $casts = [
        'tipo_pessoa' => TipoPessoa::class,
        'credit_limit' => 'decimal:2',
    ];
    
    public function salesOrders(): HasMany
    {
        return $this->hasMany(SalesOrder::class);
    }
    
    public function hasAvailableCredit(?float $amount = 0): bool
    {
        // Lógica simples de domínio
    }
}
```

### 4.6 Camada de Persistência (Database)
**Responsabilidades**:
- Armazenamento de dados
- Constraints e índices
- Migrations versionadas

**Princípios**:
- Uma migration por alteração
- Nunca editar migration já executada em produção
- Seeders idempotentes (usar `updateOrCreate`, `firstOrCreate`)

---

## 5. Padrões de Projeto Utilizados

### 5.1 Repository Pattern (Parcial)
Não implementado globalmente, mas usado em contextos específicos:
- `ToolRegistry`: Repositório de ferramentas de IA
- `RagService`: Repositório de documentos RAG

### 5.2 Service Pattern
Amplamente utilizado para lógica de negócio:
- `SalesOrderService`
- `PayrollService`
- `PricingService`
- `AiAssistantService`

### 5.3 Observer Pattern
Para eventos de modelos:
- `SalesOrderObserver`: Reage a criação/atualização de pedidos
- Registrado via `#[ObservedBy([...])]`

### 5.4 Strategy Pattern
Para múltiplos provedores de IA:
```php
// AgenteService escolhe estratégia:
if ($this->openaiAvailable()) {
    return $this->callOpenAI();
} elseif ($this->geminiAvailable()) {
    return $this->callGemini();
} else {
    return $this->fallback();
}
```

### 5.5 Factory Pattern
Via Laravel Factories:
```php
Client::factory()->count(50)->create();
SalesOrder::factory()->for($client)->create();
```

### 5.6 Facade Pattern
Via Laravel Facades:
```php
DB::transaction(function () { /* ... */ });
Cache::remember('key', $ttl, fn() => /* ... */);
Log::info('Pedido criado', ['order_id' => $order->id]);
```

---

## 6. Convenções de Código

### 6.1 Nomenclatura

#### Models
- **Singular**, PascalCase: `Client`, `SalesOrder`, `Product`
- Tabelas: **plural**, snake_case: `clients`, `sales_orders`, `products`

#### Controllers
- Sufixo `Controller`: `ClientController`, `SalesOrderController`
- Métodos RESTful: `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`

#### Livewire Components
- PascalCase: `ClientIndex`, `SalesOrderForm`
- Organizados por módulo: `app/Livewire/Vendas/PedidosVenda.php`
- Views: kebab-case: `livewire.vendas.pedidos-venda`

#### Services
- Sufixo `Service`: `SalesOrderService`, `PayrollService`
- Métodos verbos: `createOrder`, `calculateTotals`, `processPayment`

#### Rotas
- kebab-case: `/vendas/pedidos`, `/contas-bancarias`
- Nomes: dot notation: `vendas.pedidos`, `contas_bancarias.index`

### 6.2 Estrutura de Arquivos

#### Livewire Components
```php
namespace App\Livewire\Vendas;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Pedidos de Venda')]
class PedidosVenda extends Component
{
    // ...
}
```

#### Services
```php
namespace App\Services;

class SalesOrderService
{
    public function createOrder(array $data): SalesOrder
    {
        // Lógica pública
    }
    
    protected function validateOrder(array $data): void
    {
        // Lógica privada/protegida
    }
}
```

### 6.3 Comentários e Documentação

#### DocBlocks
```php
/**
 * Cria um novo pedido de venda com validações completas.
 *
 * @param  array  $data  Dados do pedido
 * @return SalesOrder
 * @throws \Exception se cliente inativo ou sem crédito
 */
public function createOrder(array $data): SalesOrder
```

#### Comentários Inline
```php
// Valida cliente antes de criar pedido
$this->validateOrder($data);

// Cria pedido em transação (reverte tudo se falhar)
return DB::transaction(function () use ($data) {
    // ...
});
```

---

## 7. Fluxo de Dados entre Módulos

### 7.1 Diagrama Geral

```
                          ┌──────────────┐
                          │   CADASTRO   │
                          │  (Base de    │
                          │   Dados)     │
                          └──────┬───────┘
                                 │
                 ┌───────────────┼───────────────┐
                 │               │               │
                 ▼               ▼               ▼
         ┌───────────┐   ┌───────────┐   ┌───────────┐
         │  COMPRAS  │   │  VENDAS   │   │    RH     │
         └─────┬─────┘   └─────┬─────┘   └─────┬─────┘
               │               │               │
               ▼               ▼               ▼
         ┌───────────┐   ┌───────────┐   ┌───────────┐
         │  ESTOQUE  │◄──┤  PRODUÇÃO │   │ FINANCEIRO│
         └───────────┘   └───────────┘   └─────▲─────┘
               │               │               │
               └───────────────┼───────────────┘
                               │
                               ▼
                         ┌───────────┐
                         │   FISCAL  │
                         └───────────┘
```

### 7.2 Eventos e Observers

#### SalesOrderObserver
```php
class SalesOrderObserver
{
    public function created(SalesOrder $order)
    {
        // Gera order_number
        // Cria log de auditoria
    }
    
    public function updated(SalesOrder $order)
    {
        if ($order->wasChanged('status')) {
            $this->onStatusChange($order);
        }
    }
    
    protected function onStatusChange(SalesOrder $order)
    {
        match ($order->status) {
            SalesOrderStatus::EmSeparacao => $this->onSeparation($order),
            SalesOrderStatus::Invoiced => $this->onInvoiced($order),
            SalesOrderStatus::Cancelado => $this->onCanceled($order),
            default => null,
        };
    }
    
    protected function onSeparation(SalesOrder $order)
    {
        // INTEGRAÇÃO COM ESTOQUE
        foreach ($order->items as $item) {
            StockMovement::create([
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'type' => 'output',
                'origin' => "Pedido #{$order->order_number}",
            ]);
            
            $item->product->decrement('stock', $item->quantity);
        }
    }
    
    protected function onInvoiced(SalesOrder $order)
    {
        // INTEGRAÇÃO COM FISCAL
        if ($order->is_fiscal) {
            FiscalNote::create([
                'sales_order_id' => $order->id,
                'client_id' => $order->client_id,
                'status' => FiscalNoteStatus::Draft,
                // ...
            ]);
        }
        
        // INTEGRAÇÃO COM FINANCEIRO
        foreach ($order->installments as $installment) {
            AccountReceivable::create([
                'client_id' => $order->client_id,
                'description_title' => "Pedido #{$order->order_number} - Parcela {$installment->installment_number}",
                'amount' => $installment->amount,
                'due_date_at' => $installment->due_date,
                'status' => ReceivableStatus::Pending,
            ]);
        }
    }
}
```

---

## 8. Segurança

### 8.1 Autenticação
- Laravel Sanctum para API
- Session para aplicação web
- Middleware `auth` protege rotas
- Middleware `admin` para áreas administrativas

### 8.2 Autorização
- Gates e Policies (planejado)
- Validação de `is_admin` em controllers
- Validação de `has_license` em middleware

### 8.3 Validação de Dados
```php
// Form Request
$validated = $request->validate([
    'name' => 'required|string|max:255',
    'email' => 'required|email|unique:clients,email',
    'taxNumber' => 'required|cpf_cnpj',
]);

// Livewire Component
protected $rules = [
    'client.name' => 'required|min:3',
    'client.email' => 'required|email',
];

$this->validate();
```

### 8.4 Proteção contra SQL Injection
- Eloquent ORM com prepared statements
- Nunca usar `DB::raw()` com input do usuário
- Usar binding de parâmetros:
```php
DB::select('SELECT * FROM clients WHERE name = ?', [$name]);
```

### 8.5 Proteção CSRF
- Token CSRF automático em formulários Blade
- Middleware `VerifyCsrfToken` ativo
- Exceções em `$except` para APIs externas

### 8.6 XSS Protection
- Blade escapa automaticamente: `{{ $var }}`
- Usar `{!! $html !!}` apenas para HTML confiável
- Sanitização de input com `strip_tags()`

---

## 9. Performance e Escalabilidade

### 9.1 Caching
```php
// Cache de configurações
$settings = Cache::remember('system_settings', 3600, fn() => 
    Setting::all()->pluck('value', 'key')
);

// Cache de consultas pesadas
$dashboard = Cache::remember("dashboard_user_{$userId}", 600, fn() =>
    DashboardMetricsService::getMetrics($userId)
);
```

### 9.2 Eager Loading
```php
// ❌ N+1 Problem
$orders = SalesOrder::all();
foreach ($orders as $order) {
    echo $order->client->name; // Cada loop faz 1 query
}

// ✅ Eager Loading
$orders = SalesOrder::with('client')->get();
foreach ($orders as $order) {
    echo $order->client->name; // 1 query apenas
}
```

### 9.3 Indexação de Banco
```php
// Migration
Schema::create('sales_orders', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('client_id');
    
    // Índices
    $table->index('client_id');
    $table->index('status');
    $table->index('order_date');
    $table->unique('order_number');
});
```

### 9.4 Filas para Tarefas Pesadas
```php
// Enviar e-mail de nota fiscal em background
dispatch(new SendNFeEmailJob($note, $client->email));

// Gerar relatório grande
dispatch(new GenerateReportJob($filters, $userId))->onQueue('reports');
```

### 9.5 Paginação
```php
// Sempre paginar grandes listagens
$clients = Client::paginate(PerPage::resolve());

// PerPage::resolve() retorna 10, 20, 50, 100 ou 200
// baseado na preferência do usuário
```

---

## 10. Testes

### 10.1 Estrutura de Testes
```
tests/
├── Feature/               # Testes de fluxo completo
│   ├── Auth/
│   ├── Cadastro/
│   ├── Vendas/
│   └── Fiscal/
├── Unit/                  # Testes de unidades isoladas
│   ├── Models/
│   ├── Services/
│   └── Helpers/
└── Pest.php               # Configuração do Pest
```

### 10.2 Convenções de Teste (Pest)
```php
// tests/Feature/Vendas/SalesOrderTest.php

use App\Models\{Client, SalesOrder};
use function Pest\Laravel\{actingAs, assertDatabaseHas};

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);
});

test('pedido é criado com sucesso', function () {
    $client = Client::factory()->create();
    
    $response = $this->post('/api/sales-orders', [
        'client_id' => $client->id,
        'items' => [
            ['product_id' => $product->id, 'quantity' => 2],
        ],
    ]);
    
    $response->assertCreated();
    assertDatabaseHas('sales_orders', ['client_id' => $client->id]);
});

test('pedido com cliente inativo é rejeitado', function () {
    $client = Client::factory()->create(['situation' => 'inactive']);
    
    expect(fn() => SalesOrderService::createOrder(['client_id' => $client->id]))
        ->toThrow(Exception::class, 'Cliente inativo');
});
```

### 10.3 Cobertura de Testes
```bash
php artisan test --coverage
# ou
./vendor/bin/pest --coverage
```

Metas:
- Models: 80%+
- Services: 90%+
- Controllers: 70%+

---

## 11. Deploy e Ambiente

### 11.1 Ambientes
- **Local**: Desenvolvimento individual
- **Staging**: Testes integrados (homologação)
- **Production**: Produção

### 11.2 Checklist de Deploy
```bash
# 1. Pull do código
git pull origin main

# 2. Instalar dependências
composer install --no-dev --optimize-autoloader
npm install && npm run build

# 3. Migrations
php artisan migrate --force

# 4. Cache de configuração
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Otimizações
php artisan optimize

# 6. Reiniciar filas
php artisan queue:restart

# 7. Verificar
php artisan about
```

### 11.3 Variáveis de Ambiente (.env)
```env
# Essenciais para produção
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...

# Banco
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=nexora_prod
DB_USERNAME=nexora_user
DB_PASSWORD=senha_forte

# Cache e Filas
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis

# URLs
APP_URL=https://erp.minhaempresa.com.br

# NF-e
NFE_AMBIENTE=producao
NFE_CERTIFICADO_PATH=/path/to/certificado.pfx
```

---

**Próximos Passos**: Consultar documentações específicas de cada módulo para detalhes de implementação.

