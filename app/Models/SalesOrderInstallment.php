<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesOrderInstallment extends Model
{
    protected $fillable = [
        'sales_order_payment_id',
        'sales_order_id',
        'installment_number',
        'amount',
        'due_date',
        'payment_date',
        'status',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'installment_number' => 'integer',
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'payment_date' => 'date',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(SalesOrderPayment::class, 'sales_order_payment_id');
    }

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function isOverdue(): bool
    {
        return $this->status === 'pending' && $this->due_date < now();
    }
}

