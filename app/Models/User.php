<?php

namespace App\Models;

use App\Traits\Loggable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, Loggable;

    protected string $logModule = 'Segurança';
    protected string $logName   = 'Usuário';

    protected $fillable = [
        'name', 'email', 'password',
        'is_admin', 'is_active', 'has_license',
        'modules', 'last_login_at',
        'avatar', 'phone', 'job_title', 'department', 'bio',
        'company_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_admin'          => 'boolean',
            'is_active'         => 'boolean',
            'has_license'       => 'boolean',
            'modules'           => 'array',
            'last_login_at'     => 'datetime',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return (bool) $this->is_admin;
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
