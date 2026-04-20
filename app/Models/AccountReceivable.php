<?php

namespace App\Models;

use App\Enums\ReceivableStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountReceivable extends Model
{
    protected $table = 'accounts_receivable';

    protected $fillable = [
        'description_title',
        'client_id',
        'chart_of_account_id',
        'amount',
        'received_amount',
        'due_date_at',
        'received_at',
        'payment_method',
        'installment_number',
        'status',
        'observation',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'received_amount'  => 'decimal:2',
        'due_date_at'      => 'date',
        'received_at'      => 'date',
        'installment_number' => 'integer',
        'status'           => ReceivableStatus::class,
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function chartOfAccount(): BelongsTo
    {
        return $this->belongsTo(PlanOfAccount::class, 'chart_of_account_id');
    }
}
