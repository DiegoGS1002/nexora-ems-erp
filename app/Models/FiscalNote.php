<?php

namespace App\Models;

use App\Enums\FiscalNoteStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FiscalNote extends Model
{
    protected $fillable = [
        'client_id',
        'client_name',
        'invoice_number',
        'series',
        'access_key',
        'type',
        'environment',
        'status',
        'protocol',
        'sefaz_message',
        'authorized_at',
        'cancel_protocol',
        'cancel_reason',
        'cancelled_at',
        'xml_path',
        'xml_cancel_path',
        'amount',
        'notes',
        'emitted_by',
    ];

    protected $casts = [
        'status'        => FiscalNoteStatus::class,
        'amount'        => 'decimal:2',
        'authorized_at' => 'datetime',
        'cancelled_at'  => 'datetime',
    ];

    /* ── Relacionamentos ───────────────────────── */

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function emittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'emitted_by');
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
        if (!$this->access_key) return '—';
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
        return !empty($this->xml_path);
    }
}

