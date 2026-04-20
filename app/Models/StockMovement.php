<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use Loggable;

    protected string $logModule = 'Estoque';
    protected string $logName   = 'Movimentação de Estoque';

    protected $fillable = [
        'product_id',
        'user_id',
        'quantity',
        'type',
        'origin',
        'unit_cost',
        'observation',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_cost' => 'decimal:2',
    ];

    /**
     * Relação com Product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relação com User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
