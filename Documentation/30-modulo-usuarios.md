# Módulo de Controle de Usuários

## 1. Visão Geral

Gerencia usuários do sistema, definindo permissões, grupos de acesso e controle de licenças.

### Funcionalidades
- CRUD de usuários
- Perfis (admin, manager, user)
- Grupos de permissões
- Controle de licenças
- Auditoria de ações
- Bloqueio/desbloqueio
- Reset de senha

---

## 2. Estrutura

### Model: `User` (Laravel padrão estendido)
```php
protected $fillable = [
    'name',
    'email',
    'password',
    'employee_id',
    'is_admin',
    'is_active',
    'has_license',
    'last_login_at',
    'avatar',
];

protected $casts = [
    'is_admin' => 'boolean',
    'is_active' => 'boolean',
    'has_license' => 'boolean',
    'last_login_at' => 'datetime',
    'email_verified_at' => 'datetime',
];
```

### Permissões (futuro com Spatie Permission)
```php
$user->givePermissionTo('create posts');
$user->hasPermissionTo('edit posts');
$user->assignRole('manager');
```

---

## 3. Funcionalidades

### Reset de Senha
```php
Password::sendResetLink(['email' => $email]);
```

### Bloqueio de Usuário
```php
$user->update(['is_active' => false]);
```

### Auditoria
```php
$logs = SystemLog::where('user_id', $userId)
    ->orderByDesc('created_at')
    ->paginate(50);
```

---

**Caminho**: `app/Models/User.php`, `app/Livewire/Administracao/Usuarios/`

