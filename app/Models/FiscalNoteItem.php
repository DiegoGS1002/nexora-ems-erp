<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FiscalNoteItem extends Model
{
    protected $fillable = [
        'fiscal_note_id',
        'item_number',
        'product_code',
        'ean',
        'description',
        'ncm',
        'cfop',
        'unit',
        'quantity',
        'unit_price',
        'total',
        'discount',
        'freight',
        'insurance',
        'other',
        'origin',
        'cst',
        'csosn',
        'icms_base',
        'icms_percent',
        'icms_amount',
        'icms_mod_bc',
        'icms_st_base',
        'icms_st_percent',
        'icms_st_amount',
        'ipi_cst',
        'ipi_base',
        'ipi_percent',
        'ipi_amount',
        'pis_cst',
        'pis_base',
        'pis_percent',
        'pis_amount',
        'cofins_cst',
        'cofins_base',
        'cofins_percent',
        'cofins_amount',
    ];

    protected $casts = [
        'quantity'       => 'decimal:4',
        'unit_price'     => 'decimal:10',
        'total'          => 'decimal:2',
        'discount'       => 'decimal:2',
        'freight'        => 'decimal:2',
        'icms_base'      => 'decimal:2',
        'icms_percent'   => 'decimal:2',
        'icms_amount'    => 'decimal:2',
        'icms_st_base'   => 'decimal:2',
        'icms_st_percent' => 'decimal:2',
        'icms_st_amount' => 'decimal:2',
        'ipi_base'       => 'decimal:2',
        'ipi_percent'    => 'decimal:2',
        'ipi_amount'     => 'decimal:2',
        'pis_base'       => 'decimal:2',
        'pis_percent'    => 'decimal:2',
        'pis_amount'     => 'decimal:2',
        'cofins_base'    => 'decimal:2',
        'cofins_percent' => 'decimal:2',
        'cofins_amount'  => 'decimal:2',
    ];

    public function fiscalNote(): BelongsTo
    {
        return $this->belongsTo(FiscalNote::class);
    }
}

