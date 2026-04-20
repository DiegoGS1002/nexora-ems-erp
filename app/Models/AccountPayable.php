<?php

namespace App\Models;

use App\Enums\PayableStatus;
use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountPayable extends Model
{
    use Loggable;

    protected string $logModule = 'Financeiro';
    protected string $logName   = 'Conta a Pagar';

    protected $table = 'accounts_payable';

    protected $fillable = [
        'description_title',
        'supplier_id',
        'chart_of_account_id',
        'amount',
        'due_date_at',
        'payment_date',
        'paid_amount',
        'status',
        'observation',
        'attachment_path',
        'is_recurring',
        'recurrence_day',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_date_at' => 'date',
        'payment_date' => 'date',
        'status' => PayableStatus::class,
        'is_recurring' => 'boolean',
        'recurrence_day' => 'integer',
    ];

    /**
     * Relationship: Supplier
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Relationship: Chart of Account
     */
    public function chartOfAccount(): BelongsTo
    {
        return $this->belongsTo(PlanOfAccount::class, 'chart_of_account_id');
    }

    /**
     * Scope: Accounts due today
     */
    public function scopeDueToday(Builder $query): Builder
    {
        return $query->where('due_date_at', today())
            ->whereIn('status', [PayableStatus::Pending->value, PayableStatus::Overdue->value]);
    }

    /**
     * Scope: Accounts due this week (next 7 days)
     */
    public function scopeDueThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('due_date_at', [today(), today()->addDays(7)])
            ->whereIn('status', [PayableStatus::Pending->value, PayableStatus::Overdue->value]);
    }
}
