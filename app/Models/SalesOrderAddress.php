<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesOrderAddress extends Model
{
    protected $fillable = [
        'sales_order_id',
        'type',
        'zip_code',
        'street',
        'number',
        'complement',
        'district',
        'city',
        'state',
        'country',
        'ibge_code',
    ];

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function getFullAddressAttribute(): string
    {
        return trim(implode(', ', array_filter([
            $this->street,
            $this->number,
            $this->complement,
            $this->district,
            $this->city,
            $this->state,
            $this->zip_code,
        ])));
    }
}

