# Guia de Início Rápido - Nexora ERP

## 🚀 Setup Inicial (5 minutos)

### 1. Pré-requisitos
```bash
# Verificar versões
php --version    # Deve ser 8.4+
composer --version
node --version   # Deve ser 20+
npm --version
mysql --version  # MySQL 8.x ou 9.x
```

### 2. Clonar e Instalar
```bash
# Clone o repositório
git clone https://github.com/DiegoGS1002/nexora-ems-erp.git
cd nexora-ems-erp

# Setup automático (recomendado)
composer run setup
# Este comando executa:
# - composer install
# - copia .env.example para .env
# - php artisan key:generate
# - php artisan migrate
# - npm install
# - npm run build
```

### 3. Configurar Banco de Dados
```bash
# Editar .env
nano .env

# Configure:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nexora
DB_USERNAME=root
DB_PASSWORD=sua_senha
```

### 4. Migrar e Popular
```bash
# Executar migrations
php artisan migrate

# Criar dados de exemplo
php artisan db:seed

# Ou recriar tudo do zero
php artisan migrate:fresh --seed
```

### 5. Iniciar Servidor
```bash
# Desenvolvimento completo (servidor + filas + vite)
composer run dev

# Ou manualmente em 3 terminais:
# Terminal 1: Servidor Laravel
php artisan serve

# Terminal 2: Vite (hot reload)
npm run dev

# Terminal 3: Filas
php artisan queue:work
```

### 6. Acessar Sistema
```
URL: http://localhost:8000
Login: admin@nexora.com
Senha: password
```

---

## 📁 Estrutura de Arquivos Importantes

```
nexora-ems-erp/
├── app/
│   ├── Livewire/           # Componentes reativos (a maior parte da UI)
│   │   ├── Vendas/
│   │   │   ├── PedidosVenda.php      # Listagem de pedidos
│   │   │   └── PedidoForm.php        # Formulário de pedido
│   │   └── ...
│   ├── Models/             # Modelos Eloquent
│   │   ├── Client.php
│   │   ├── Product.php
│   │   └── SalesOrder.php
│   ├── Services/           # Lógica de negócio
│   │   └── SalesOrderService.php
│   └── Http/
│       └── Controllers/    # Controllers (principalmente API)
│
├── resources/
│   ├── css/
│   │   ├── app.css         # CSS principal
│   │   ├── _components.css # Componentes UI
│   │   └── _tables.css     # Estilos de tabelas
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php  # Layout base
│       └── livewire/          # Views dos componentes
│
├── routes/
│   ├── web.php             # Inclui todos os módulos
│   ├── vendas.php          # Rotas de vendas
│   ├── cadastro.php        # Rotas de cadastros
│   └── api.php             # API REST
│
├── database/
│   ├── migrations/         # Estrutura do banco
│   └── seeders/           # Dados iniciais
│
└── Documentation/          # 📚 Esta documentação
```

---

## 🎯 Tarefas Comuns

### Criar Novo Módulo (Exemplo: Relatórios)

#### 1. Criar Componente Livewire
```bash
php artisan make:livewire Relatorios/Index
```

Isso cria:
- `app/Livewire/Relatorios/Index.php`
- `resources/views/livewire/relatorios/index.blade.php`

#### 2. Editar Componente
```php
// app/Livewire/Relatorios/Index.php
namespace App\Livewire\Relatorios;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Relatórios')]
class Index extends Component
{
    public $search = '';
    
    public function render()
    {
        $reports = Report::where('name', 'like', "%{$this->search}%")
            ->paginate(15);
        
        return view('livewire.relatorios.index', compact('reports'));
    }
}
```

#### 3. Criar Rota
```bash
# Criar arquivo routes/relatorios.php
touch routes/relatorios.php
```

```php
// routes/relatorios.php
use App\Livewire\Relatorios\Index;

Route::get('/relatorios', Index::class)->name('relatorios.index');
```

#### 4. Incluir no web.php
```php
// routes/web.php
require __DIR__ . '/relatorios.php';
```

#### 5. Adicionar ao Whitelist do Middleware
```php
// app/Http/Middleware/MaintenanceERP.php
private $whitelist = [
    // ...
    'relatorios.*',  // Permite acessar o módulo
];
```

#### 6. Adicionar ao Menu (opcional)
```blade
{{-- resources/views/layouts/app.blade.php --}}
<a href="{{ route('relatorios.index') }}" class="nav-link">
    <i class="fas fa-chart-bar"></i> Relatórios
</a>
```

---

### Criar Nova Migration

```bash
# Criar tabela
php artisan make:migration create_reports_table

# Adicionar coluna
php artisan make:migration add_status_to_reports_table
```

```php
// database/migrations/xxxx_create_reports_table.php
public function up()
{
    Schema::create('reports', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->text('description')->nullable();
        $table->string('type'); // pdf, excel, etc.
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('reports');
}
```

```bash
# Executar migration
php artisan migrate
```

---

### Criar Novo Model

```bash
php artisan make:model Report
```

```php
// app/Models/Report.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type',
        'is_active',
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
    ];
}
```

---

### Criar Service

```bash
# Criar manualmente
touch app/Services/ReportService.php
```

```php
// app/Services/ReportService.php
namespace App\Services;

use App\Models\Report;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function generateReport(array $filters): Report
    {
        return DB::transaction(function () use ($filters) {
            // Lógica complexa aqui
            $report = Report::create([
                'name' => $filters['name'],
                'type' => 'pdf',
            ]);
            
            // ... processar dados
            
            return $report;
        });
    }
}
```

---

### Criar API Endpoint

```php
// routes/api.php
use App\Http\Controllers\Api\ReportController;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('reports', ReportController::class);
});
```

```bash
# Criar controller
php artisan make:controller Api/ReportController --api
```

```php
// app/Http/Controllers/Api/ReportController.php
namespace App\Http\Controllers\Api;

use App\Models\Report;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function index(): JsonResponse
    {
        $reports = Report::paginate(15);
        return response()->json($reports);
    }
    
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:pdf,excel,csv',
        ]);
        
        $report = Report::create($validated);
        
        return response()->json($report, 201);
    }
}
```

---

## 🧪 Executar Testes

```bash
# Todos os testes
php artisan test

# Testes específicos
php artisan test --filter=SalesOrderTest

# Com cobertura
php artisan test --coverage

# Usando Pest diretamente
./vendor/bin/pest
./vendor/bin/pest --filter="pedido é criado"
```

---

## 🐛 Debug e Logs

### Ver Logs em Tempo Real
```bash
tail -f storage/logs/laravel.log
```

### Usar Tinker para Debug
```bash
php artisan tinker

# Exemplos:
>>> Client::count()
=> 50

>>> $order = SalesOrder::first()
>>> $order->client->name
=> "Cliente ABC"

>>> User::where('is_admin', true)->get()
```

### Limpar Cache
```bash
# Limpar tudo
php artisan optimize:clear

# Ou individualmente:
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 🔧 Configurações Importantes

### Alterar Ambiente
```env
# .env
APP_ENV=local     # local, staging, production
APP_DEBUG=true    # true apenas em desenvolvimento
```

### Configurar E-mail (opcional)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu-email@gmail.com
MAIL_PASSWORD=sua-senha-app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=seu-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Configurar Filas (Redis)
```bash
# Instalar Redis
sudo apt install redis-server  # Ubuntu/Debian
brew install redis              # macOS

# Configurar .env
QUEUE_CONNECTION=redis
```

---

## 📦 Comandos Artisan Customizados

```bash
# RAG (Assistente IA)
php artisan rag:seed            # Popular base de conhecimento
php artisan rag:search "termo"  # Testar busca semântica

# Seeders específicos
php artisan db:seed --class=SettingsSeeder
php artisan db:seed --class=RagKnowledgeSeeder
php artisan db:seed --class=SefazDiagnosticsSeeder
```

---

## 🚨 Troubleshooting

### Problema: "No application encryption key"
```bash
php artisan key:generate
```

### Problema: "SQLSTATE[HY000] [2002] Connection refused"
```bash
# Verificar se MySQL está rodando
sudo service mysql status

# Iniciar MySQL
sudo service mysql start
```

### Problema: Assets não encontrados (404)
```bash
# Criar link simbólico
php artisan storage:link

# Rebuild assets
npm run build
```

### Problema: Permissões de Storage
```bash
sudo chown -R $USER:$USER storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### Problema: Livewire não atualiza
```bash
# Limpar view cache
php artisan view:clear

# Rebuild assets
npm run build
```

---

## 📚 Documentação Adicional

- **Arquitetura Completa**: [37-arquitetura-sistema.md](./37-arquitetura-sistema.md)
- **API REST**: [38-api-rest.md](./38-api-rest.md)
- **Módulo de Vendas**: [12-modulo-pedidos-venda.md](./12-modulo-pedidos-venda.md)
- **Módulo Fiscal (NF-e)**: [17-modulo-notas-fiscais.md](./17-modulo-notas-fiscais.md)
- **Assistente IA**: [36-modulo-assistente-ia.md](./36-modulo-assistente-ia.md)
- **Todas as Docs**: [README.md](./README.md)

---

## 💡 Dicas de Produtividade

### Aliases úteis (adicionar ao `.bashrc` ou `.zshrc`)
```bash
alias art="php artisan"
alias tinker="php artisan tinker"
alias migrate="php artisan migrate"
alias seed="php artisan db:seed"
alias fresh="php artisan migrate:fresh --seed"
alias test="php artisan test"
alias serve="php artisan serve"
```

### VS Code Extensions recomendadas
- Laravel Extra Intellisense
- Laravel Blade Snippets
- PHP Intelephense
- Tailwind CSS IntelliSense
- Livewire Language Support

### PhpStorm Plugins
- Laravel Idea
- PHP Annotations
- Tailwind CSS
- .env files support

---

## 🎓 Próximos Passos

1. ✅ Configure o ambiente local
2. ✅ Explore o dashboard (`/dashboard`)
3. 📖 Leia a documentação de [Arquitetura](./37-arquitetura-sistema.md)
4. 🔍 Estude o módulo de [Pedidos de Venda](./12-modulo-pedidos-venda.md) (exemplo completo)
5. 🛠️ Crie seu primeiro componente Livewire
6. 🧪 Escreva testes para suas funcionalidades
7. 🤖 Configure o Assistente IA (opcional)

---

**Precisa de ajuda?** Consulte a documentação específica de cada módulo ou use o Assistente IA no canto inferior direito do sistema.

**Última Atualização**: 28 de maio de 2026

