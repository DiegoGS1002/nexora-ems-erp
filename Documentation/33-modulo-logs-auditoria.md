# Módulo de Logs de Auditoria

## 1. Visão Geral

O módulo de Logs registra todas as ações importantes do sistema para auditoria, rastreabilidade e conformidade. Usa a trait `Loggable` para automatizar registros.

### Funcionalidades Principais
- Registro automático de ações
- Trait Loggable para models
- Detalhamento de alterações
- Filtros avançados
- Retenção configurável
- Exportação de logs

---

## 2. Estrutura Técnica

### 2.1 Model: `SystemLog`

```php
protected $fillable = [
    'user_id',
    'module',
    'action',
    'description',
    'ip_address',
    'user_agent',
    'model_type',
    'model_id',
    'old_values',
    'new_values',
    'severity',
];
```

### 2.2 Trait: `Loggable`

```php
trait Loggable
{
    protected string $logModule = 'Sistema';
    protected string $logName = 'Registro';
    
    protected static function bootLoggable()
    {
        static::created(function ($model) {
            SystemLog::create([
                'user_id' => auth()->id(),
                'module' => $model->logModule,
                'action' => 'created',
                'description' => "{$model->logName} criado",
                'model_type' => get_class($model),
                'model_id' => $model->id,
            ]);
        });
        
        static::updated(function ($model) {
            SystemLog::create([
                'user_id' => auth()->id(),
                'module' => $model->logModule,
                'action' => 'updated',
                'description' => "{$model->logName} atualizado",
                'model_type' => get_class($model),
                'model_id' => $model->id,
                'old_values' => json_encode($model->getOriginal()),
                'new_values' => json_encode($model->getAttributes()),
            ]);
        });
    }
}
```

### 2.3 Uso em Models

```php
class Client extends Model
{
    use HasFactory, Loggable;
    
    protected string $logModule = 'Cadastros';
    protected string $logName = 'Cliente';
}
```

---

## 3. Tipos de Log

### 3.1 Ações Automáticas
- `created`: Criação de registro
- `updated`: Atualização
- `deleted`: Exclusão
- `restored`: Restauração (soft delete)

### 3.2 Ações Manuais
```php
LogService::info(
    action: 'LOGIN',
    description: "Usuário logou no sistema",
    module: 'Autenticação'
);

LogService::warning(
    action: 'TENTATIVA_ACESSO_NEGADO',
    description: "Tentativa de acesso a área restrita",
    module: 'Segurança',
    details: ['url' => $url]
);

LogService::error(
    action: 'ERRO_SEFAZ',
    description: "Erro ao transmitir NF-e: {$erro}",
    module: 'Fiscal',
    details: ['nfe_id' => $nfeId, 'erro' => $erro]
);
```

---

## 4. Filtros e Consultas

```php
// Logs de um usuário
$logs = SystemLog::where('user_id', $userId)
    ->orderByDesc('created_at')
    ->paginate(50);

// Logs de um módulo
$logs = SystemLog::where('module', 'Vendas')
    ->whereBetween('created_at', [$startDate, $endDate])
    ->get();

// Logs de um registro específico
$logs = SystemLog::where('model_type', Client::class)
    ->where('model_id', $clientId)
    ->get();

// Logs críticos
$logs = SystemLog::where('severity', 'error')
    ->whereDate('created_at', today())
    ->get();
```

---

## 5. Retenção

```php
// Limpar logs antigos (comando agendado)
SystemLog::where('created_at', '<', now()->subMonths(6))
    ->delete();
```

---

**Caminho**: `app/Models/SystemLog.php`, `app/Traits/Loggable.php`, `app/Services/LogService.php`

