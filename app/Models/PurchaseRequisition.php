<?php

namespace App\Models;

use App\Enums\SolicitacaoCompraStatus;
use App\Enums\SolicitacaoCompraPrioridade;
use App\Models\PurchaseRequisitionItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseRequisition extends Model
{
    protected $fillable = [
        'number',
        'title',
        'status',
        'priority',
        'requester_id',
        'department',
        'needed_by',
        'justification',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'purchase_order_id',
        'cotacao_id',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'status'      => SolicitacaoCompraStatus::class,
        'priority'    => SolicitacaoCompraPrioridade::class,
        'needed_by'   => 'date',
        'approved_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($req) {
            if (empty($req->number)) {
                $req->number = 'REQ-' . str_pad(
                    (static::max('id') ?? 0) + 1,
                    6, '0', STR_PAD_LEFT
                );
            }
            if (auth()->check()) {
                if (empty($req->created_by)) {
                    $req->created_by = auth()->id();
                }
                if (empty($req->requester_id)) {
                    $req->requester_id = auth()->id();
                }
            }
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseRequisitionItem::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function cotacao(): BelongsTo
    {
        return $this->belongsTo(Cotacao::class);
    }

    public function getTotalEstimado(): float
    {
        return (float) $this->items->sum(fn($i) => (float) $i->quantity * (float) $i->estimated_price);
    }

    public function canEdit(): bool
    {
        return in_array($this->status, [
            SolicitacaoCompraStatus::Rascunho,
            SolicitacaoCompraStatus::AguardandoAprovacao,
        ]);
    }
}

