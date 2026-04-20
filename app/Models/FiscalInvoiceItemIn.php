<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FiscalInvoiceItemIn extends Model
{
    protected $table = 'fiscal_invoice_items_in';

    protected $fillable = [
        'fiscal_invoice_in_id',
        'product_id',
        'product_code',
        'product_name',
        'ncm',
        'cfop',
        'unit',
        'quantity',
        'unit_price',
        'total_price',
        'icms_base',
        'icms_rate',
        'icms_value',
        'ipi_value',
        'pis_value',
        'cofins_value',
    ];

    protected $casts = [
        'quantity'   => 'decimal:4',
        'unit_price' => 'decimal:4',
        'total_price'=> 'decimal:2',
        'icms_base'  => 'decimal:2',
        'icms_rate'  => 'decimal:4',
        'icms_value' => 'decimal:2',
        'ipi_value'  => 'decimal:2',
        'pis_value'  => 'decimal:2',
        'cofins_value'=> 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(FiscalInvoiceIn::class, 'fiscal_invoice_in_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

