<?php

namespace App\Models;

use App\Enums\FiscalNoteStatus;
use App\Enums\FiscalConsumer;
use App\Enums\FiscalDestination;
use App\Enums\FiscalEmissionType;
use App\Enums\FiscalOperationType;
use App\Enums\FiscalPresence;
use App\Enums\FiscalPurpose;
use App\Enums\FiscalFreightMode;
use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FiscalNote extends Model
{
    use HasFactory, Loggable;

    protected string $logModule = 'Fiscal';

    protected string $logName = 'Nota Fiscal';

    protected $fillable = [
        'company_id',
        'client_id',
        'client_name',
        'sales_order_id',
        'invoice_number',
        'series',
        'access_key',
        'type',
        'environment',
        'status',
        'protocol',
        'receipt_number',
        'sefaz_message',
        'authorized_at',
        'cancel_protocol',
        'cancel_reason',
        'cancelled_at',
        'xml_path',
        'xml_sent_path',
        'xml_cancel_path',
        'amount',
        'operation_nature',
        'operation_type',
        'destination',
        'consumer_type',
        'presence_type',
        'purpose',
        'emission_type',
        'referenced_access_key',
        'intermediary_cnpj',
        'intermediary_identifier',
        'delivery_location',
        'pickup_location',
        'freight_mode',
        'transport_data',
        'payment_details',
        'change_amount',
        'invoice_data',
        'installment_data',
        'notes',
        'emitted_by',
    ];

    protected $casts = [
        'status' => FiscalNoteStatus::class,
        'operation_type' => FiscalOperationType::class,
        'destination' => FiscalDestination::class,
        'consumer_type' => FiscalConsumer::class,
        'presence_type' => FiscalPresence::class,
        'purpose' => FiscalPurpose::class,
        'emission_type' => FiscalEmissionType::class,
        'delivery_location' => 'array',
        'pickup_location' => 'array',
        'freight_mode' => FiscalFreightMode::class,
        'transport_data' => 'array',
        'payment_details' => 'array',
        'change_amount' => 'decimal:2',
        'invoice_data' => 'array',
        'installment_data' => 'array',
        'amount' => 'decimal:2',
        'authorized_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /* ── Relacionamentos ───────────────────────── */

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id');
    }

    public function emittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'emitted_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(FiscalNoteItem::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(FiscalNoteEvent::class)->latest();
    }

    /* ── Helpers ───────────────────────────────── */

    public function getDisplayClientAttribute(): string
    {
        if ($this->client) {
            return $this->client->social_name ?? $this->client->name ?? '—';
        }

        return $this->client_name ?? '—';
    }

    public function getFormattedAccessKeyAttribute(): string
    {
        if (! $this->access_key) {
            return '—';
        }

        // Formata: 4 grupos de 11 dígitos separados por espaço
        return implode(' ', str_split($this->access_key, 11));
    }

    public function isEditable(): bool
    {
        return $this->status === FiscalNoteStatus::Draft;
    }

    public function isCancellable(): bool
    {
        return $this->status === FiscalNoteStatus::Authorized;
    }

    public function hasXml(): bool
    {
        return ! empty($this->xml_path);
    }
}
