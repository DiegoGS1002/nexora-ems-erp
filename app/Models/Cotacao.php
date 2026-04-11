<?php

namespace App\Models;

use App\Enums\CotacaoStatus;
use Illuminate\Database\Eloquent\Model;
use App\Models\CotacaoItem;
use App\Models\CotacaoResposta;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cotacao extends Model
{
    protected $table = 'cotacoes';

    protected $fillable = [
        'number',
        'title',
        'status',
        'deadline_date',
        'notes',
        'purchase_order_id',
        'created_by',
    ];

    protected $casts = [
        'status'        => CotacaoStatus::class,
        'deadline_date' => 'date',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($cotacao) {
            if (empty($cotacao->number)) {
                $cotacao->number = 'COT-' . str_pad(
                    (static::max('id') ?? 0) + 1,
                    6, '0', STR_PAD_LEFT
                );
            }
            if (auth()->check() && empty($cotacao->created_by)) {
                $cotacao->created_by = auth()->id();
            }
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(CotacaoItem::class);
    }

    public function respostas(): HasMany
    {
        return $this->hasMany(CotacaoResposta::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getSupplierIdsAttribute(): array
    {
        return $this->respostas()->distinct()->pluck('supplier_id')->toArray();
    }
}



