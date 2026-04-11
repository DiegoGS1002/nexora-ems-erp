<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CotacaoResposta extends Model
{
    protected $fillable = [
        'cotacao_id',
        'cotacao_item_id',
        'supplier_id',
        'unit_price',
        'delivery_days',
        'notes',
        'selected',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'selected'   => 'boolean',
    ];

    public function cotacao(): BelongsTo
    {
        return $this->belongsTo(Cotacao::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(CotacaoItem::class, 'cotacao_item_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}

