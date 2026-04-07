<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BaccaratAccount extends Model
{
    protected $table = 'baccarat_accounts';

    protected $fillable = [
        'name',
        'description',
        'bank_name',
        'agency',
        'number',
        'type',
        'balance',
        'predicted_balance',
        'color',
        'chart_of_account_id',
        'is_active',
        'is_reconciled',
        'last_reconciled_at',
    ];

    protected $casts = [
        'balance'            => 'decimal:2',
        'predicted_balance'  => 'decimal:2',
        'is_active'          => 'boolean',
        'is_reconciled'      => 'boolean',
        'last_reconciled_at' => 'date',
    ];

    /* ─────────────────────────────────────
    | RELATIONSHIPS
    ─────────────────────────────────────*/

    public function chartOfAccount(): BelongsTo
    {
        return $this->belongsTo(PlanOfAccount::class, 'chart_of_account_id');
    }

    /* ─────────────────────────────────────
    | ACCESSORS / HELPERS
    ─────────────────────────────────────*/

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'corrente'      => 'Conta Corrente',
            'poupanca'      => 'Poupança',
            'caixa_interno' => 'Caixa Interno',
            'digital'       => 'Carteira Digital',
            default         => '-',
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'corrente'      => 'building-bank',
            'poupanca'      => 'piggy-bank',
            'caixa_interno' => 'cash',
            'digital'       => 'device-mobile',
            default         => 'bank',
        };
    }

    public function getCardColorAttribute(): string
    {
        if ($this->color) {
            return $this->color;
        }

        // Auto-assign color based on bank name
        $bank = strtolower($this->bank_name ?? '');
        if (str_contains($bank, 'nubank') || str_contains($bank, 'nu '))      return '#7C3AED';
        if (str_contains($bank, 'itaú') || str_contains($bank, 'itau'))        return '#F97316';
        if (str_contains($bank, 'bradesco'))                                    return '#CC0000';
        if (str_contains($bank, 'brasil') || str_contains($bank, 'bb'))        return '#1C5E9A';
        if (str_contains($bank, 'caixa') || str_contains($bank, 'cef'))        return '#0D7C3D';
        if (str_contains($bank, 'santander'))                                   return '#EC0000';
        if (str_contains($bank, 'inter'))                                       return '#FF8700';
        if (str_contains($bank, 'c6') || str_contains($bank, 'c6bank'))        return '#242424';
        if (str_contains($bank, 'sicredi') || str_contains($bank, 'sicoob'))   return '#00802B';
        if ($this->type === 'caixa_interno')                                    return '#0F766E';
        if ($this->type === 'digital')                                          return '#6D28D9';
        return '#1E3A5F'; // default dark blue
    }

    public function getFormattedBalanceAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->balance, 2, ',', '.');
    }

    public function getFormattedPredictedBalanceAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->predicted_balance, 2, ',', '.');
    }
}
