<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceTableItem extends Model
{
    protected $fillable = [
        'price_table_id',
        'product_id',
        'price',
        'minimum_price',
        'promotional_price',
        'promotional_valid_from',
        'promotional_valid_until',
        'quantity_from',
        'quantity_to',
        'quantity_price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'minimum_price' => 'decimal:2',
        'promotional_price' => 'decimal:2',
        'promotional_valid_from' => 'date',
        'promotional_valid_until' => 'date',
        'quantity_from' => 'decimal:3',
        'quantity_to' => 'decimal:3',
        'quantity_price' => 'decimal:2',
    ];

    public function priceTable(): BelongsTo
    {
        return $this->belongsTo(PriceTable::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getEffectivePrice(float $quantity = 1): float
    {
        // Verifica se há promoção válida
        if ($this->hasValidPromotion()) {
            return (float) $this->promotional_price;
        }

        // Verifica se há preço por quantidade
        if ($this->quantity_from && $quantity >= $this->quantity_from) {
            if (!$this->quantity_to || $quantity <= $this->quantity_to) {
                return (float) $this->quantity_price;
            }
        }

        return (float) $this->price;
    }

    public function hasValidPromotion(): bool
    {
        if (!$this->promotional_price) {
            return false;
        }

        $now = now();

        if ($this->promotional_valid_from && $this->promotional_valid_from > $now) {
            return false;
        }

        if ($this->promotional_valid_until && $this->promotional_valid_until < $now) {
            return false;
        }

        return true;
    }
}

