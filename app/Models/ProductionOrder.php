<?php

namespace App\Models;

use App\Enums\ProductionOrderStatus;
use App\Models\ProductionItem;
use App\Models\ProductionOrderProduct;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionOrder extends Model
{
    protected $fillable = [
        'name',
        'description',
        'product_id',
        'target_quantity',
        'produced_quantity',
        'start_date',
        'end_date',
        'status',
        'estimated_cost',
        'lot_number',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'target_quantity'   => 'decimal:3',
        'produced_quantity' => 'decimal:3',
        'estimated_cost'    => 'decimal:2',
        'start_date'        => 'datetime',
        'end_date'          => 'datetime',
        'status'            => ProductionOrderStatus::class,
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProductionItem::class);
    }

    public function orderProducts(): HasMany
    {
        return $this->hasMany(ProductionOrderProduct::class);
    }

    public function getProgressPercentageAttribute(): int
    {
        if ($this->relationLoaded('orderProducts') && $this->orderProducts->count() > 0) {
            $total    = (float) $this->orderProducts->sum('target_quantity');
            $produced = (float) $this->orderProducts->sum('produced_quantity');
            if (!$total) return 0;
            return min(100, (int) (($produced / $total) * 100));
        }

        if (!$this->target_quantity || $this->target_quantity == 0) {
            return 0;
        }
        return min(100, (int) (($this->produced_quantity / $this->target_quantity) * 100));
    }
}
