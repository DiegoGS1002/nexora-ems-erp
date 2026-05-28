# Módulo de Configurações do Sistema

## 1. Visão Geral

O módulo de Configurações centraliza todas as configurações globais do sistema através de uma interface key-value, permitindo personalização sem alterar código.

### Funcionalidades Principais
- Configurações key-value
- Agrupamento por categoria
- Tipos: texto, número, booleano, JSON
- Cache automático
- Interface amigável
- Backup de configurações

---

## 2. Estrutura Técnica

### 2.1 Model: `Setting`

```php
protected $fillable = [
    'key',
    'value',
    'type',
    'category',
    'description',
];

protected $casts = [
    'value' => 'json',
];
```

### 2.2 Helper: `setting()`

```php
function setting(string $key, $default = null)
{
    return Cache::rememberForever("setting_{$key}", function () use ($key, $default) {
        return Setting::where('key', $key)->value('value') ?? $default;
    });
}

// Uso
$systemName = setting('app.name', 'Nexora ERP');
$allowNegativeStock = setting('stock.allow_negative', false);
```

### 2.3 Categorias

```php
'general' => [
    'app.name' => 'Nome do Sistema',
    'app.logo' => 'Caminho do Logo',
],

'email' => [
    'email.from_address' => 'E-mail Remetente',
    'email.from_name' => 'Nome Remetente',
],

'fiscal' => [
    'nfe.environment' => 'Ambiente NF-e',
    'nfe.timeout' => 'Timeout SEFAZ',
],

'stock' => [
    'stock.allow_negative' => 'Permitir Estoque Negativo',
    'stock.average_cost' => 'Usar Custo Médio',
],
```

### 2.4 Livewire: `Settings`

Interface com abas por categoria, formulários dinâmicos conforme tipo.

---

## 3. Exemplos

```php
// Criar configuração
Setting::create([
    'key' => 'app.name',
    'value' => 'Nexora ERP',
    'type' => 'string',
    'category' => 'general',
]);

// Ler
$name = setting('app.name');

// Atualizar
Setting::where('key', 'app.name')
    ->update(['value' => 'Meu ERP']);
    
Cache::forget('setting_app.name');
```

---

**Caminho**: `app/Models/Setting.php`, `app/Helpers/helpers.php`, `app/Livewire/Administracao/Configuracoes.php`

