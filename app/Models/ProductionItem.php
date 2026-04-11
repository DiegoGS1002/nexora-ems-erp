<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionItem extends Model
{
    protected $fillable = [
        'production_order_id',
        'component_id',
        'required_qty',
        'consumed_qty',
    ];

    protected $casts = [
        'required_qty' => 'decimal:3',
        'consumed_qty' => 'decimal:3',
    ];

    public function productionOrder(): BelongsTo
    {
        return $this->belongsTo(ProductionOrder::class);
    }

    public function component(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'component_id');
    }
}

