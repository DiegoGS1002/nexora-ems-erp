<?php

namespace App\Models;

use App\Enums\PurchaseOrderStatus;
use App\Enums\PurchaseOrderOrigin;
use Illuminate\Database\Eloquent\Model;
use App\Models\PurchaseOrderItem;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'order_number',
        'supplier_id',
        'buyer_id',
        'status',
        'origin',
        'order_date',
        'expected_delivery_date',
        'received_at',
        'payment_condition',
        'payment_method',
        'freight_type',
        'carrier_id',
        'delivery_address',
        'subtotal',
        'discount_amount',
        'shipping_amount',
        'other_expenses',
        'total_amount',
        'notes',
        'notes_supplier',
        'approved_by',
        'approved_at',
        'created_by',
    ];

    protected $casts = [
        'status'                 => PurchaseOrderStatus::class,
        'origin'                 => PurchaseOrderOrigin::class,
        'order_date'             => 'datetime',
        'expected_delivery_date' => 'date',
        'received_at'            => 'date',
        'approved_at'            => 'datetime',
        'subtotal'               => 'decimal:2',
        'discount_amount'        => 'decimal:2',
        'shipping_amount'        => 'decimal:2',
        'other_expenses'         => 'decimal:2',
        'total_amount'           => 'decimal:2',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'PC-' . str_pad(
                    (static::max('id') ?? 0) + 1,
                    6, '0', STR_PAD_LEFT
                );
            }
            if (empty($order->order_date)) {
                $order->order_date = now();
            }
            if (auth()->check() && empty($order->created_by)) {
                $order->created_by = auth()->id();
            }
        });
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function calculateTotals(): void
    {
        $this->subtotal     = $this->items->sum('total');
        $this->total_amount = $this->subtotal
            - ($this->discount_amount ?? 0)
            + ($this->shipping_amount ?? 0)
            + ($this->other_expenses  ?? 0);
    }

    public function approve(User $user): void
    {
        $this->update([
            'status'      => PurchaseOrderStatus::Aprovado,
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);
    }

    public function canEdit(): bool
    {
        return in_array($this->status, [
            PurchaseOrderStatus::Rascunho,
            PurchaseOrderStatus::AguardandoAprovacao,
        ]);
    }
}


