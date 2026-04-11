<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CotacaoResposta;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CotacaoItem extends Model
{
    protected $fillable = [
        'cotacao_id',
        'product_id',
        'description',
        'sku',
        'unit',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
    ];

    public function cotacao(): BelongsTo
    {
        return $this->belongsTo(Cotacao::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function respostas(): HasMany
    {
        return $this->hasMany(CotacaoResposta::class, 'cotacao_item_id');
    }

    public function getBestPrice(): ?float
    {
        $min = $this->respostas()->where('unit_price', '>', 0)->min('unit_price');
        return $min !== null ? (float) $min : null;
    }
}


