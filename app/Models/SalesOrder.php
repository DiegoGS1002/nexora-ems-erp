<?php

namespace App\Models;

use App\Enums\SalesOrderStatus;
use App\Enums\TipoOperacaoVenda;
use App\Enums\CanalVenda;
use App\Enums\OrigemPedido;
use App\Enums\TipoFrete;
use App\Enums\StatusSeparacao;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesOrder extends Model
{
    protected $fillable = [
        'order_number',
        'client_id',
        'user_id',
        'seller_id',
        'is_fiscal',
        'status',
        'order_date',
        'delivery_date',
        'expected_delivery_date',
        'operation_type',
        'sales_channel',
        'origin',
        'company_branch',

        // Dados do Cliente
        'client_cpf_cnpj',
        'client_ie',
        'client_type',
        'client_credit_limit',
        'client_situation',
        'client_contact_phone',
        'client_contact_email',

        // Tabela de Preço
        'price_table_id',
        'minimum_margin',

        // Totais
        'subtotal',
        'gross_total',
        'discount_amount',
        'additions_amount',
        'shipping_amount',
        'insurance_amount',
        'other_expenses',
        'net_total',

        // Impostos
        'tax_amount',
        'icms_base',
        'icms_amount',
        'icms_st_amount',
        'ipi_amount',
        'pis_amount',
        'cofins_amount',
        'fcp_amount',

        'total_amount',
        'payment_condition',

        // Logística
        'carrier_id',
        'freight_type',
        'gross_weight',
        'net_weight',
        'volumes',
        'tracking_code',
        'delivery_notes',

        // Separação
        'separation_status',
        'separator_user_id',
        'separation_date',
        'conference_date',

        // Faturamento
        'fiscal_note_id',
        'nfe_number',
        'nfe_series',
        'nfe_key',
        'nfe_protocol',
        'nfe_status',
        'nfe_issued_at',

        // Observações
        'internal_notes',
        'customer_notes',
        'fiscal_notes_obs',

        // Aprovações
        'approved_by',
        'approved_at',
        'approval_reason',
        'needs_approval',

        // Auditoria
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_fiscal'              => 'boolean',
        'needs_approval'         => 'boolean',
        'status'                 => SalesOrderStatus::class,
        'operation_type'         => TipoOperacaoVenda::class,
        'sales_channel'          => CanalVenda::class,
        'origin'                 => OrigemPedido::class,
        'freight_type'           => TipoFrete::class,
        'separation_status'      => StatusSeparacao::class,
        'order_date'             => 'datetime',
        'delivery_date'          => 'date',
        'expected_delivery_date' => 'date',
        'separation_date'        => 'datetime',
        'conference_date'        => 'datetime',
        'nfe_issued_at'          => 'datetime',
        'approved_at'            => 'datetime',

        // Decimais
        'subtotal'          => 'decimal:2',
        'gross_total'       => 'decimal:2',
        'discount_amount'   => 'decimal:2',
        'additions_amount'  => 'decimal:2',
        'shipping_amount'   => 'decimal:2',
        'insurance_amount'  => 'decimal:2',
        'other_expenses'    => 'decimal:2',
        'net_total'         => 'decimal:2',
        'tax_amount'        => 'decimal:2',
        'icms_base'         => 'decimal:2',
        'icms_amount'       => 'decimal:2',
        'icms_st_amount'    => 'decimal:2',
        'ipi_amount'        => 'decimal:2',
        'pis_amount'        => 'decimal:2',
        'cofins_amount'     => 'decimal:2',
        'fcp_amount'        => 'decimal:2',
        'total_amount'      => 'decimal:2',
        'client_credit_limit' => 'decimal:2',
        'minimum_margin'    => 'decimal:2',
        'gross_weight'      => 'decimal:3',
        'net_weight'        => 'decimal:3',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'PV-' . str_pad(
                    (static::max('id') ?? 0) + 1,
                    6,
                    '0',
                    STR_PAD_LEFT
                );
            }

            if (empty($order->order_date)) {
                $order->order_date = now();
            }

            if (auth()->check() && empty($order->created_by)) {
                $order->created_by = auth()->id();
            }
        });

        static::updating(function ($order) {
            if (auth()->check()) {
                $order->updated_by = auth()->id();
            }
        });
    }

    // Relacionamentos
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(SalesOrderAddress::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SalesOrderPayment::class);
    }

    public function installments(): HasMany
    {
        return $this->hasMany(SalesOrderInstallment::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(SalesOrderLog::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(SalesOrderAttachment::class);
    }

    public function priceTable(): BelongsTo
    {
        return $this->belongsTo(PriceTable::class);
    }

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class);
    }

    public function separator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'separator_user_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function fiscalNote(): BelongsTo
    {
        return $this->belongsTo(FiscalNote::class);
    }

    // Métodos auxiliares
    public function billingAddress()
    {
        return $this->addresses()->where('type', 'billing')->first();
    }

    public function deliveryAddress()
    {
        return $this->addresses()->where('type', 'delivery')->first();
    }

    public function collectionAddress()
    {
        return $this->addresses()->where('type', 'collection')->first();
    }

    public function calculateTotals(): void
    {
        // Calcula subtotal dos itens
        $this->gross_total = $this->items->sum('total');
        $this->subtotal = $this->items->sum('subtotal');

        // Calcula impostos
        $this->icms_amount = $this->items->sum('icms_amount');
        $this->icms_st_amount = $this->items->sum('icms_st_amount');
        $this->ipi_amount = $this->items->sum('ipi_amount');
        $this->pis_amount = $this->items->sum('pis_amount');
        $this->cofins_amount = $this->items->sum('cofins_amount');
        $this->fcp_amount = $this->items->sum('fcp_amount');
        $this->tax_amount = $this->icms_amount + $this->icms_st_amount + $this->ipi_amount +
                           $this->pis_amount + $this->cofins_amount + $this->fcp_amount;

        // Calcula total líquido
        $this->net_total = $this->subtotal - $this->discount_amount + $this->additions_amount;

        // Calcula total final
        $this->total_amount = $this->net_total + $this->shipping_amount +
                             $this->insurance_amount + $this->other_expenses;

        if ($this->is_fiscal) {
            $this->total_amount += $this->tax_amount;
        }
    }

    public function logAction(string $action, string $description = null, $oldStatus = null, $newStatus = null): void
    {
        $this->logs()->create([
            'user_id' => auth()->id(),
            'action' => $action,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'description' => $description,
            'changes' => $this->getChanges(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function approve(User $user, string $reason = null): bool
    {
        $this->approved_by = $user->id;
        $this->approved_at = now();
        $this->approval_reason = $reason;
        $this->needs_approval = false;
        $this->status = SalesOrderStatus::Approved;

        $this->save();
        $this->logAction('approved', 'Pedido aprovado por ' . $user->name);

        return true;
    }

    public function cancel(string $reason = null): bool
    {
        $this->status = SalesOrderStatus::Cancelled;
        $this->save();

        $this->logAction('cancelled', 'Pedido cancelado. Motivo: ' . $reason);

        return true;
    }

    public function canEdit(): bool
    {
        return in_array($this->status, [
            SalesOrderStatus::Draft,
            SalesOrderStatus::Aberto,
        ]);
    }

    public function canCancel(): bool
    {
        return !in_array($this->status, [
            SalesOrderStatus::Invoiced,
            SalesOrderStatus::Delivered,
            SalesOrderStatus::Cancelled,
        ]);
    }
}

