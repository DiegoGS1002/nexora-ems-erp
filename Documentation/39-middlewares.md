# Middlewares do Sistema

## 1. Visão Geral

Middlewares são filtros HTTP que interceptam requisições antes de chegarem aos controllers ou componentes Livewire. O Nexora ERP utiliza middlewares customizados para implementar regras de negócio específicas, segurança e controle de acesso.

---

## 2. Middlewares Customizados

### 2.1 MaintenanceERP

**Caminho**: `app/Http/Middleware/MaintenanceERP.php`

#### Propósito
Bloqueia acesso a rotas ainda não implementadas, exibindo uma página de "Em Breve" (em desenvolvimento).

#### Funcionamento
```php
public function handle(Request $request, Closure $next): Response
{
    $whitelist = [
        'login',
        'logout',
        'dashboard.*',
        'clients.*',
        'products.*',
        'vendas.*',
        'fiscal.*',
        'financeiro.*',
        // ... rotas permitidas
    ];
    
    $currentRoute = $request->route()->getName();
    
    // Verifica se a rota está no whitelist
    foreach ($whitelist as $pattern) {
        if (Str::is($pattern, $currentRoute)) {
            return $next($request);
        }
    }
    
    // Rota não permitida, exibe tela "Em Breve"
    return response()->view('system.desenvolvimento', [], 503);
}
```

#### Uso
Registrado globalmente no `bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        MaintenanceERP::class,
    ]);
})
```

#### Adicionar Nova Rota Acessível
Ao criar um novo módulo funcional:
```php
// Adicionar padrão ao whitelist em MaintenanceERP.php
private $whitelist = [
    // ...
    'novo-modulo.*',  // Permite todas as rotas do módulo
];
```

---

### 2.2 EnsureUserIsAdmin

**Caminho**: `app/Http/Middleware/EnsureUserIsAdmin.php`

#### Propósito
Garante que apenas administradores acessem rotas administrativas (usuários, logs, configurações).

#### Funcionamento
```php
public function handle(Request $request, Closure $next): Response
{
    if (!auth()->check()) {
        abort(401, 'Não autenticado');
    }
    
    if (!auth()->user()->is_admin) {
        abort(403, 'Acesso negado. Apenas administradores.');
    }
    
    return $next($request);
}
```

#### Uso
Aplicado em rotas específicas:
```php
// routes/administracao.php
Route::middleware('admin')->group(function () {
    Route::get('/empresas', EmpresasIndex::class)->name('companies.index');
    Route::get('/users', UsersIndex::class)->name('users.index');
});
```

#### Alias
Registrado como `admin` em `bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'admin' => EnsureUserIsAdmin::class,
    ]);
})
```

---

### 2.3 EnforceMidnightSession

**Caminho**: `app/Http/Middleware/EnforceMidnightSession.php`

#### Propósito
Força logout automático de usuários quando a data muda (após meia-noite), garantindo que sessões não ultrapassem o dia do login.

#### Funcionamento
```php
public function handle(Request $request, Closure $next): Response
{
    if (!auth()->check()) {
        return $next($request);
    }
    
    $user = auth()->user();
    
    // Verifica se last_login_at é de um dia anterior
    if ($user->last_login_at && $user->last_login_at->isYesterday()) {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')->with('message', 'Sua sessão expirou. Por favor, faça login novamente.');
    }
    
    return $next($request);
}
```

#### Uso
Registrado globalmente no grupo `web`:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        EnforceMidnightSession::class,
    ]);
})
```

#### Casos de Uso
- Empresas com turnos noturnos que exigem nova autenticação a cada dia
- Conformidade com políticas de segurança corporativa
- Auditoria de acesso por dia útil

---

### 2.4 DetectAiModule

**Caminho**: `app/Http/Middleware/DetectAiModule.php`

#### Propósito
Detecta automaticamente o módulo atual baseado na URL e compartilha com o assistente de IA para fornecer contexto específico.

#### Funcionamento
```php
public function handle(Request $request, Closure $next): Response
{
    $path = $request->path();
    $firstSegment = explode('/', $path)[0] ?? 'geral';
    
    // Mapeia segmento de URL para módulo
    $moduleMap = [
        'vendas' => 'vendas',
        'compras' => 'compras',
        'fiscal' => 'fiscal',
        'financeiro' => 'financeiro',
        'estoque' => 'estoque',
        'rh' => 'rh',
        'producao' => 'producao',
        'logistica' => 'logistica',
        'cadastro' => 'cadastro',
        'clients' => 'cadastro',
        'products' => 'cadastro',
        'suppliers' => 'cadastro',
        'suporte' => 'suporte',
    ];
    
    $module = $moduleMap[$firstSegment] ?? 'geral';
    
    // Compartilha com todas as views
    View::share('aiModule', $module);
    
    return $next($request);
}
```

#### Uso
Registrado globalmente no grupo `web`:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        DetectAiModule::class,
    ]);
})
```

#### Integração com IA
```php
// No componente AiChatBubble
$module = view()->shared('aiModule', 'geral');

// Busca contexto específico do módulo
$context = config("ai_contexts.{$module}");

// Monta prompt contextualizado
$prompt = $context['prompt'] ?? 'Você é um assistente do ERP';
```

---

## 3. Middlewares Nativos do Laravel (Utilizados)

### 3.1 Authenticate
**Alias**: `auth`

Garante que o usuário está autenticado.

```php
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class);
});
```

Se não autenticado, redireciona para `/login`.

---

### 3.2 ThrottleRequests
**Alias**: `throttle`

Limita taxa de requisições (rate limiting).

```php
// API: 60 requisições por minuto
Route::middleware('throttle:60,1')->group(function () {
    // Rotas da API
});

// Login: 5 tentativas por minuto
Route::post('/login', [SessionController::class, 'store'])
    ->middleware('throttle:5,1');
```

---

### 3.3 VerifyCsrfToken

Protege contra ataques CSRF em formulários.

**Automático em rotas `web`**.

Exceções em `app/Http/Middleware/VerifyCsrfToken.php`:
```php
protected $except = [
    '/api/*',           // API não usa CSRF
    '/webhook/*',       // Webhooks externos
];
```

---

### 3.4 SubstituteBindings

Resolve route model binding automaticamente.

```php
Route::get('/clients/{client}/edit', function (Client $client) {
    // $client já é uma instância do model
    return view('clients.edit', compact('client'));
});
```

---

## 4. Grupos de Middleware

### 4.1 Grupo `web`
Aplicado automaticamente a rotas em `routes/web.php`.

**Middlewares incluídos**:
- `EncryptCookies`
- `AddQueuedCookiesToResponse`
- `StartSession`
- `ShareErrorsFromSession`
- `VerifyCsrfToken`
- `SubstituteBindings`
- **MaintenanceERP** (customizado)
- **EnforceMidnightSession** (customizado)
- **DetectAiModule** (customizado)

### 4.2 Grupo `api`
Aplicado automaticamente a rotas em `routes/api.php`.

**Middlewares incluídos**:
- `ThrottleRequests:60,1` (60 req/min)
- `SubstituteBindings`

### 4.3 Grupo Customizado: `admin`
Criado para proteger rotas administrativas.

```php
// Registrado como alias
$middleware->alias([
    'admin' => EnsureUserIsAdmin::class,
]);

// Uso
Route::middleware('admin')->group(function () {
    Route::get('/users', UsersIndex::class);
    Route::get('/logs', LogsIndex::class);
});
```

---

## 5. Ordem de Execução

A ordem dos middlewares é importante:

```
1. TrustProxies           # Confia em proxies (se aplicável)
2. HandleCors             # CORS
3. PreventRequestsDuringMaintenance  # Modo manutenção Laravel
4. TrimStrings            # Remove espaços de strings
5. ConvertEmptyStringsToNull  # Converte "" em null
6. EncryptCookies         # Criptografa cookies
7. StartSession           # Inicia sessão
8. Authenticate           # Verifica autenticação
9. EnforceMidnightSession # Valida sessão por data (nosso)
10. EnsureUserIsAdmin     # Valida admin (nosso, se aplicável)
11. VerifyCsrfToken       # Valida token CSRF
12. MaintenanceERP        # Bloqueia rotas em dev (nosso)
13. DetectAiModule        # Detecta módulo IA (nosso)
14. Controller/Component  # Finalmente chega ao destino
```

---

## 6. Criar Novo Middleware

### 6.1 Gerar Middleware
```bash
php artisan make:middleware CheckLicense
```

### 6.2 Implementar Lógica
```php
// app/Http/Middleware/CheckLicense.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckLicense
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        if (!$user || !$user->has_license) {
            return redirect()->route('license.expired')
                ->with('error', 'Sua licença expirou. Contate o administrador.');
        }
        
        return $next($request);
    }
}
```

### 6.3 Registrar Middleware

#### Opção 1: Globalmente
```php
// bootstrap/app.php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        CheckLicense::class,
    ]);
})
```

#### Opção 2: Como Alias
```php
// bootstrap/app.php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'license' => CheckLicense::class,
    ]);
})

// Uso em rotas
Route::middleware('license')->group(function () {
    // Rotas protegidas
});
```

#### Opção 3: Diretamente em Rotas
```php
use App\Http\Middleware\CheckLicense;

Route::middleware(CheckLicense::class)->group(function () {
    // Rotas
});
```

---

## 7. Middleware em Componentes Livewire

Livewire components podem ter seus próprios middlewares:

```php
namespace App\Livewire\Admin;

use Livewire\Component;

class UsersManagement extends Component
{
    public function getMiddleware()
    {
        return ['auth', 'admin'];
    }
    
    public function render()
    {
        return view('livewire.admin.users-management');
    }
}
```

Ou via atributos (Laravel 11+):
```php
use Livewire\Attributes\Middleware;

#[Middleware(['auth', 'admin'])]
class UsersManagement extends Component
{
    // ...
}
```

---

## 8. Middleware Termináveis

Middlewares podem executar código **após** a resposta ser enviada ao cliente.

```php
namespace App\Http\Middleware;

class LogRequest
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
    
    public function terminate(Request $request, Response $response)
    {
        // Executa após resposta enviada
        Log::info('Request processed', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'status' => $response->status(),
        ]);
    }
}
```

**Uso**: Logs, analytics, limpeza de recursos.

---

## 9. Teste de Middlewares

### 9.1 Teste de Acesso Negado (Admin)
```php
test('usuário não admin não acessa página de usuários', function () {
    $user = User::factory()->create(['is_admin' => false]);
    
    $this->actingAs($user)
        ->get('/users')
        ->assertForbidden(); // 403
});

test('administrador acessa página de usuários', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    
    $this->actingAs($admin)
        ->get('/users')
        ->assertOk(); // 200
});
```

### 9.2 Teste de Sessão Expirada
```php
test('usuário é deslogado após meia-noite', function () {
    $user = User::factory()->create([
        'last_login_at' => now()->subDay(), // Ontem
    ]);
    
    $this->actingAs($user)
        ->get('/dashboard')
        ->assertRedirect('/login')
        ->assertSessionHas('message', 'Sua sessão expirou');
});
```

### 9.3 Teste de Rate Limit
```php
test('login é limitado a 5 tentativas por minuto', function () {
    // 5 tentativas bem-sucedidas
    for ($i = 0; $i < 5; $i++) {
        $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong',
        ])->assertStatus(422); // Validação falhou
    }
    
    // 6ª tentativa é bloqueada
    $this->post('/login', [
        'email' => 'test@example.com',
        'password' => 'wrong',
    ])->assertStatus(429); // Too Many Requests
});
```

---

## 10. Boas Práticas

### 10.1 Middlewares Devem Ser Leves
❌ Ruim (lógica pesada):
```php
public function handle(Request $request, Closure $next)
{
    $user = auth()->user();
    
    // Consulta pesada em cada requisição
    $permissions = Permission::where('user_id', $user->id)
        ->with('roles.modules')
        ->get();
    
    // ...
    return $next($request);
}
```

✅ Bom (usar cache):
```php
public function handle(Request $request, Closure $next)
{
    $user = auth()->user();
    
    $permissions = Cache::remember(
        "user_permissions_{$user->id}",
        3600,
        fn() => Permission::where('user_id', $user->id)->with('roles')->get()
    );
    
    // ...
    return $next($request);
}
```

### 10.2 Middlewares Devem Ter Responsabilidade Única
❌ Ruim (faz muitas coisas):
```php
public function handle(Request $request, Closure $next)
{
    // Verifica autenticação
    if (!auth()->check()) { /* ... */ }
    
    // Verifica licença
    if (!$user->has_license) { /* ... */ }
    
    // Verifica IP
    if (!$this->isAllowedIp($request->ip())) { /* ... */ }
    
    // Loga requisição
    Log::info('Request', ['url' => $request->url()]);
    
    return $next($request);
}
```

✅ Bom (middlewares separados):
- `Authenticate`: Verifica autenticação
- `CheckLicense`: Verifica licença
- `CheckIpWhitelist`: Verifica IP
- `LogRequest`: Registra requisição

```php
Route::middleware(['auth', 'license', 'ip-whitelist', 'log'])->group(function () {
    // Rotas
});
```

### 10.3 Documentar Exceções
Se middleware altera comportamento padrão:
```php
/**
 * Força logout de usuários após meia-noite (troca de dia).
 * 
 * Útil para empresas que exigem nova autenticação a cada dia útil.
 * 
 * @see EnforceMidnightSession
 */
```

---

## 11. Debugging de Middlewares

### 11.1 Listar Middlewares de uma Rota
```bash
php artisan route:list --name=clients.index
```

Saída:
```
GET /clients .... clients.index
Middleware: web, auth, MaintenanceERP, EnforceMidnightSession
```

### 11.2 Ver Ordem de Execução
```bash
php artisan route:list --columns=uri,name,middleware
```

### 11.3 Debug em Middleware
```php
public function handle(Request $request, Closure $next)
{
    Log::debug('Middleware executado', [
        'middleware' => static::class,
        'user' => auth()->id(),
        'route' => $request->route()->getName(),
    ]);
    
    return $next($request);
}
```

---

## 12. Exemplos Práticos

### 12.1 Middleware para Verificar Módulo Ativo
```php
class CheckModuleEnabled
{
    public function handle(Request $request, Closure $next, string $module)
    {
        $enabledModules = auth()->user()->modules ?? [];
        
        if (!in_array($module, $enabledModules)) {
            abort(403, "Módulo '{$module}' não está habilitado para este usuário.");
        }
        
        return $next($request);
    }
}

// Uso
Route::middleware('module:vendas')->group(function () {
    Route::get('/vendas/pedidos', PedidosVenda::class);
});
```

### 12.2 Middleware para Logs de Auditoria
```php
class AuditLog
{
    public function terminate(Request $request, Response $response)
    {
        if ($request->user()) {
            SystemLog::create([
                'user_id' => $request->user()->id,
                'action' => $request->method() . ' ' . $request->path(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'response_code' => $response->status(),
            ]);
        }
    }
}
```

### 12.3 Middleware para Injetar Dependência
```php
class InjectCompany
{
    public function handle(Request $request, Closure $next)
    {
        $company = Company::where('is_default', true)->first();
        
        // Disponibiliza em toda a aplicação
        app()->instance('current_company', $company);
        view()->share('company', $company);
        
        return $next($request);
    }
}
```

---

**Resumo**: Middlewares são essenciais para separar concerns (autenticação, autorização, logging) da lógica de negócio. O Nexora ERP utiliza middlewares customizados para implementar regras específicas como controle de módulos em desenvolvimento e contexto de IA.

