# Services e Helpers do Sistema

## 1. Visão Geral

Documentação dos principais Services e Helpers reutilizáveis do sistema que implementam lógica de negócio compartilhada.

---

## 2. Services Principais

### 2.1 BrasilAPIService
**Caminho**: `app/Services/BrasilAPIService.php`

Integração com BrasilAPI para consultas de CEP e CNPJ.

```php
class BrasilAPIService
{
    public function consultarCep(string $cep): ?array
    {
        $cep = preg_replace('/\D/', '', $cep);
        
        $response = Http::get("https://brasilapi.com.br/api/cep/v1/{$cep}");
        
        if ($response->successful()) {
            return $response->json();
        }
        
        return null;
    }
    
    public function consultarCNPJ(string $cnpj): ?array
    {
        $cnpj = preg_replace('/\D/', '', $cnpj);
        
        $response = Http::get("https://brasilapi.com.br/api/cnpj/v1/{$cnpj}");
        
        return $response->successful() ? $response->json() : null;
    }
}
```

### 2.2 LogService
**Caminho**: `app/Services/LogService.php`

Facilita criação de logs de auditoria.

```php
class LogService
{
    public static function info(string $action, string $description, string $module = 'Sistema', array $details = []): void
    {
        SystemLog::create([
            'user_id' => auth()->id(),
            'module' => $module,
            'action' => $action,
            'description' => $description,
            'severity' => 'info',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'old_values' => json_encode($details),
        ]);
    }
    
    public static function warning(string $action, string $description, string $module, array $details = []): void
    {
        // Similar, com severity: warning
    }
    
    public static function error(string $action, string $description, string $module, array $details = []): void
    {
        // Similar, com severity: error
    }
}
```

### 2.3 PerPage
**Caminho**: `app/Support/PerPage.php`

Resolve paginação configurável por usuário.

```php
class PerPage
{
    public static function resolve(): int
    {
        return request()->query('per_page', session('per_page', 15));
    }
    
    public static function options(): array
    {
        return [10, 20, 50, 100, 200];
    }
}
```

---

## 3. Helpers Globais

**Caminho**: `app/Helpers/helpers.php`

### 3.1 setting()
```php
function setting(string $key, $default = null)
{
    return Cache::rememberForever("setting_{$key}", function () use ($key, $default) {
        return Setting::where('key', $key)->value('value') ?? $default;
    });
}
```

### 3.2 formatCurrency()
```php
function formatCurrency(float $value): string
{
    return 'R$ ' . number_format($value, 2, ',', '.');
}
```

### 3.3 formatCPF() e formatCNPJ()
```php
function formatCPF(string $cpf): string
{
    return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
}

function formatCNPJ(string $cnpj): string
{
    return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cnpj);
}
```

### 3.4 calculateTaxes()
```php
function calculateTaxes(float $baseValue, array $percentages): array
{
    $taxes = [];
    
    foreach ($percentages as $name => $percent) {
        $taxes[$name] = $baseValue * ($percent / 100);
    }
    
    $taxes['total'] = array_sum($taxes);
    
    return $taxes;
}
```

---

## 4. Traits Reutilizáveis

### 4.1 Loggable
Já documentado em [33-modulo-logs-auditoria.md](./33-modulo-logs-auditoria.md)

### 4.2 HasUuid
```php
trait HasUuid
{
    protected static function bootHasUuid()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
    
    public function getIncrementing()
    {
        return false;
    }
    
    public function getKeyType()
    {
        return 'string';
    }
}
```

---

## 5. Enums Úteis

### 5.1 TipoPessoa
```php
enum TipoPessoa: string
{
    case Fisica = 'fisica';
    case Juridica = 'juridica';
}
```

### 5.2 PayableStatus / ReceivableStatus
```php
enum PayableStatus: string
{
    case Pending = 'pending';
    case Overdue = 'overdue';
    case Paid = 'paid';
    case Canceled = 'canceled';
}
```

### 5.3 SalesOrderStatus
```php
enum SalesOrderStatus: string
{
    case Draft = 'draft';
    case Aberto = 'aberto';
    case Aprovado = 'aprovado';
    case EmSeparacao = 'em_separacao';
    case Enviado = 'enviado';
    case Entregue = 'entregue';
    case Invoiced = 'invoiced';
    case Cancelado = 'cancelado';
}
```

---

**Caminhos**:
- `app/Services/`
- `app/Support/`
- `app/Helpers/helpers.php`
- `app/Traits/`
- `app/Enums/`

