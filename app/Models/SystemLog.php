<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemLog extends Model
{
    protected $fillable = [
        'level',
        'action',
        'module',
        'description',
        'ip',
        'user_id',
        'user_name',
        'user_email',
        'context',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    /* ── Relationships ─────────────────────────────────── */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /* ── Computed Attributes ──────────────────────────── */

    public function getLevelLabelAttribute(): string
    {
        return match ($this->level) {
            'success' => 'Sucesso',
            'warning' => 'Aviso',
            'error'   => 'Erro',
            default   => 'Desconhecido',
        };
    }

    public function getLevelBadgeClassAttribute(): string
    {
        return match ($this->level) {
            'success' => 'nx-badge-success',
            'warning' => 'nx-badge-warning',
            'error'   => 'nx-badge-danger',
            default   => 'nx-badge-neutral',
        };
    }

    public function getModuleBadgeClassAttribute(): string
    {
        return match ($this->module) {
            'Segurança'  => 'nx-badge-info',
            'Cadastros'  => 'nx-badge-neutral',
            'Vendas'     => 'nx-badge-success',
            'Financeiro' => 'nx-badge-warning',
            'Compras'    => 'nx-badge-neutral',
            'RH'         => 'nx-badge-info',
            'Estoque'    => 'nx-badge-neutral',
            'Fiscal'     => 'nx-badge-warning',
            'Transporte' => 'nx-badge-info',
            'Sistema'    => 'nx-badge-neutral',
            default      => 'nx-badge-neutral',
        };
    }
}

