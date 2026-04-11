<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesOrderPayment extends Model
{
    protected $fillable = [
        'sales_order_id',
        'payment_condition',
        'payment_method',
        'installments',
        'total_amount',
    ];

    protected $casts = [
        'installments' => 'integer',
        'total_amount' => 'decimal:2',
    ];

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function installments(): HasMany
    {
        return $this->hasMany(SalesOrderInstallment::class);
    }
}

