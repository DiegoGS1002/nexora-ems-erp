<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanOfAccount extends Model
{
    protected $table = 'plans_of_accounts';

    protected $fillable = [
        'parent_id',
        'code',
        'name',
        'description',
        'type',
        'is_selectable',
        'is_active',
    ];

    protected $casts = [
        'is_selectable' => 'boolean',
        'is_active'     => 'boolean',
    ];

    /* ──────────────────────────────────────
     | RELATIONSHIPS
     ──────────────────────────────────────*/

    public function parent(): BelongsTo
    {
        return $this->belongsTo(PlanOfAccount::class, 'parent_id');
    }

    /** Direct children — used in tree rendering */
    public function children(): HasMany
    {
        return $this->hasMany(PlanOfAccount::class, 'parent_id')->orderBy('code');
    }

    /* ──────────────────────────────────────
     | ACCESSORS / HELPERS
     ──────────────────────────────────────*/

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'receita' => 'Receita',
            'despesa' => 'Despesa',
            'ativo'   => 'Ativo',
            'passivo' => 'Passivo',
            default   => '-',
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'receita' => '#10B981',
            'despesa' => '#EF4444',
            'ativo'   => '#3B82F6',
            'passivo' => '#F59E0B',
            default   => '#94A3B8',
        };
    }

    public function getTypeCssClassAttribute(): string
    {
        return match ($this->type) {
            'receita' => 'nx-type--receita',
            'despesa' => 'nx-type--despesa',
            'ativo'   => 'nx-type--ativo',
            'passivo' => 'nx-type--passivo',
            default   => 'nx-type--default',
        };
    }

    public function isSynthetic(): bool
    {
        return $this->children()->exists();
    }
}
