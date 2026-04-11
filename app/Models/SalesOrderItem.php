<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesOrderItem extends Model
{
    protected $fillable = [
        'sales_order_id',
        'product_id',
        'sku',
        'ean',
        'description',
        'unit',
        'quantity',
        'unit_price',
        'discount',
        'discount_percent',
        'addition',
        'addition_percent',
        'tax_amount',
        'subtotal',
        'total',
        'notes',

        // Controle de estoque
        'batch',
        'expiry_date',
        'serial_number',
        'stock_location',

        // Fiscal
        'cfop',
        'ncm',
        'cst',
        'csosn',
        'origin',

        // ICMS
        'icms_base',
        'icms_percent',
        'icms_amount',

        // ICMS ST
        'icms_st_base',
        'icms_st_percent',
        'icms_st_amount',

        // IPI
        'ipi_base',
        'ipi_percent',
        'ipi_amount',

        // PIS
        'pis_base',
        'pis_percent',
        'pis_amount',

        // COFINS
        'cofins_base',
        'cofins_percent',
        'cofins_amount',

        // FCP
        'fcp_base',
        'fcp_percent',
        'fcp_amount',
    ];

    protected $casts = [
        'quantity'         => 'decimal:3',
        'unit_price'       => 'decimal:2',
        'discount'         => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'addition'         => 'decimal:2',
        'addition_percent' => 'decimal:2',
        'tax_amount'       => 'decimal:2',
        'subtotal'         => 'decimal:2',
        'total'            => 'decimal:2',
        'expiry_date'      => 'date',

        // ICMS
        'icms_base'    => 'decimal:2',
        'icms_percent' => 'decimal:2',
        'icms_amount'  => 'decimal:2',

        // ICMS ST
        'icms_st_base'    => 'decimal:2',
        'icms_st_percent' => 'decimal:2',
        'icms_st_amount'  => 'decimal:2',

        // IPI
        'ipi_base'    => 'decimal:2',
        'ipi_percent' => 'decimal:2',
        'ipi_amount'  => 'decimal:2',

        // PIS
        'pis_base'    => 'decimal:2',
        'pis_percent' => 'decimal:2',
        'pis_amount'  => 'decimal:2',

        // COFINS
        'cofins_base'    => 'decimal:2',
        'cofins_percent' => 'decimal:2',
        'cofins_amount'  => 'decimal:2',

        // FCP
        'fcp_base'    => 'decimal:2',
        'fcp_percent' => 'decimal:2',
        'fcp_amount'  => 'decimal:2',
    ];

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            // Calcula desconto se foi informado em porcentagem
            if ($item->discount_percent > 0) {
                $item->discount = ($item->quantity * $item->unit_price) * ($item->discount_percent / 100);
            }

            // Calcula acréscimo se foi informado em porcentagem
            if ($item->addition_percent > 0) {
                $item->addition = ($item->quantity * $item->unit_price) * ($item->addition_percent / 100);
            }

            // Calcula subtotal
            $item->subtotal = ($item->quantity * $item->unit_price) - $item->discount + $item->addition;

            // Calcula impostos se for fiscal
            if ($item->salesOrder && $item->salesOrder->is_fiscal) {
                $item->calculateTaxes();
            }

            // Calcula total
            $item->total = $item->subtotal + $item->tax_amount;
        });
    }

    public function calculateTaxes(): void
    {
        $baseValue = $this->subtotal;

        // ICMS
        if ($this->icms_percent > 0) {
            $this->icms_base = $baseValue;
            $this->icms_amount = $baseValue * ($this->icms_percent / 100);
        }

        // ICMS ST
        if ($this->icms_st_percent > 0) {
            $this->icms_st_base = $baseValue;
            $this->icms_st_amount = $baseValue * ($this->icms_st_percent / 100);
        }

        // IPI
        if ($this->ipi_percent > 0) {
            $this->ipi_base = $baseValue;
            $this->ipi_amount = $baseValue * ($this->ipi_percent / 100);
        }

        // PIS
        if ($this->pis_percent > 0) {
            $this->pis_base = $baseValue;
            $this->pis_amount = $baseValue * ($this->pis_percent / 100);
        }

        // COFINS
        if ($this->cofins_percent > 0) {
            $this->cofins_base = $baseValue;
            $this->cofins_amount = $baseValue * ($this->cofins_percent / 100);
        }

        // FCP
        if ($this->fcp_percent > 0) {
            $this->fcp_base = $baseValue;
            $this->fcp_amount = $baseValue * ($this->fcp_percent / 100);
        }

        // Total de impostos
        $this->tax_amount = $this->icms_amount + $this->icms_st_amount + $this->ipi_amount +
                           $this->pis_amount + $this->cofins_amount + $this->fcp_amount;
    }
}

