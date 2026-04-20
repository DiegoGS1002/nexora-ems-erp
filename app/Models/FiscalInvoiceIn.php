<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FiscalInvoiceIn extends Model
{
    use SoftDeletes;

    protected $table = 'fiscal_invoices_in';

    protected $fillable = [
        'supplier_id',
        'supplier_name',
        'supplier_cnpj',
        'supplier_ie',
        'purchase_order_id',
        'invoice_number',
        'series',
        'access_key',
        'doc_type',
        'issue_date',
        'entry_date',
        'cfop',
        'operation_nature',
        'status',
        'products_total',
        'shipping_total',
        'insurance_total',
        'other_expenses',
        'discount_total',
        'tax_total',
        'total_value',
        'xml_path',
        'raw_xml',
        'stock_movement_id',
        'account_payable_id',
        'escriturada_at',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'issue_date'      => 'date',
        'entry_date'      => 'date',
        'escriturada_at'  => 'datetime',
        'products_total'  => 'decimal:2',
        'shipping_total'  => 'decimal:2',
        'insurance_total' => 'decimal:2',
        'other_expenses'  => 'decimal:2',
        'discount_total'  => 'decimal:2',
        'tax_total'       => 'decimal:2',
        'total_value'     => 'decimal:2',
    ];

    /* ── Relacionamentos ───────────────────────── */

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(FiscalInvoiceItemIn::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function accountPayable(): BelongsTo
    {
        return $this->belongsTo(AccountPayable::class);
    }

    /* ── Helpers ───────────────────────────────── */

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'digitada'              => '#6B7280',
            'importada'             => '#3B82F6',
            'validada'              => '#8B5CF6',
            'aguardando_conferencia'=> '#F59E0B',
            'escriturada'           => '#10B981',
            'cancelada'             => '#EF4444',
            default                 => '#6B7280',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'digitada'              => 'Digitada',
            'importada'             => 'Importada',
            'validada'              => 'Validada',
            'aguardando_conferencia'=> 'Aguard. Conferência',
            'escriturada'           => 'Escriturada',
            'cancelada'             => 'Cancelada',
            default                 => ucfirst($this->status),
        };
    }

    public function isEscrituravel(): bool
    {
        return in_array($this->status, ['validada', 'aguardando_conferencia', 'importada', 'digitada']);
    }

    public function isCancellable(): bool
    {
        return !in_array($this->status, ['escriturada', 'cancelada']);
    }
}

