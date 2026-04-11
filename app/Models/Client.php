<?php

namespace App\Models;

use App\Enums\TipoPessoa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Client extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'tipo_pessoa',
        'name',
        'social_name',
        'taxNumber',
        'inscricao_estadual',
        'email',
        'phone_number',
        'address',
        'address_zip_code',
        'address_street',
        'address_number',
        'address_complement',
        'address_district',
        'address_city',
        'address_state',
        'credit_limit',
        'payment_condition_default',
        'situation',
        'price_table_id',
        'discount_limit',
    ];

    protected $casts = [
        'tipo_pessoa'    => TipoPessoa::class,
        'credit_limit'   => 'decimal:2',
        'discount_limit' => 'decimal:2',
    ];

    public function getFullAddress(): string
    {
        return trim(implode(', ', array_filter([
            $this->address_street,
            $this->address_number,
            $this->address_complement,
            $this->address_district,
            $this->address_city,
            $this->address_state,
            $this->address_zip_code,
        ])));
    }

    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function priceTable(): BelongsTo
    {
        return $this->belongsTo(PriceTable::class);
    }

    public function salesOrders(): HasMany
    {
        return $this->hasMany(SalesOrder::class);
    }

    public function isActive(): bool
    {
        return $this->situation === 'active';
    }

    public function isDefaulter(): bool
    {
        return $this->situation === 'defaulter';
    }

    public function hasAvailableCredit(?float $amount = 0): bool
    {
        if (!$this->credit_limit) {
            return true; // Sem limite definido = crédito ilimitado
        }

        $usedCredit = $this->salesOrders()
            ->whereIn('status', ['approved', 'invoiced'])
            ->sum('total_amount');

        return ($usedCredit + ($amount ?? 0)) <= $this->credit_limit;
    }
}
