<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FiscalEntryRecord extends Model
{
    protected $fillable = ['fiscal_invoice_in_id', 'item_id', 'taxes', 'credit_allowed', 'posted_at'];

    protected $casts = ['taxes' => 'array', 'credit_allowed' => 'boolean', 'posted_at' => 'datetime'];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(FiscalInvoiceIn::class, 'fiscal_invoice_in_id');
    }
}
