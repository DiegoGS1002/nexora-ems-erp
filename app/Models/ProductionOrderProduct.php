<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionOrderProduct extends Model
{
    protected $fillable = [
        'production_order_id',
        'product_id',
        'target_quantity',
        'produced_quantity',
    ];

    protected $casts = [
        'target_quantity'   => 'decimal:3',
        'produced_quantity' => 'decimal:3',
    ];

    public function productionOrder(): BelongsTo
    {
        return $this->belongsTo(ProductionOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getProgressPercentageAttribute(): int
    {
        if (!$this->target_quantity || $this->target_quantity == 0) {
            return 0;
        }
        return min(100, (int) (($this->produced_quantity / $this->target_quantity) * 100));
    }
}

