<?php

namespace App\Models;

use App\Enums\PayrollStatus;
use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payroll extends Model
{
    use Loggable;

    protected string $logModule = 'RH';
    protected string $logName   = 'Folha de Pagamento';
    protected $fillable = [
        'employee_id',
        'reference_month',
        'base_salary',
        'total_earnings',
        'total_deductions',
        'net_salary',
        'status',
        'payment_date',
        'observations',
    ];

    protected $casts = [
        'reference_month' => 'date',
        'base_salary' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'status' => PayrollStatus::class,
        'payment_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employees::class, 'employee_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
    }

    /**
     * Recalculate totals based on items
     */
    public function recalculate(): void
    {
        $items = $this->items;

        $this->total_earnings = $items->where('type', 'earning')->sum('amount');
        $this->total_deductions = $items->where('type', 'deduction')->sum('amount');
        $this->net_salary = $this->base_salary + $this->total_earnings - $this->total_deductions;

        $this->save();
    }
}
