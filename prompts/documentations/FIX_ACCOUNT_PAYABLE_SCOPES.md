# Fix: BadMethodCallException - AccountPayable::dueToday()

## Problem
The application was throwing a `BadMethodCallException` when trying to access the `/accounts_payable` route:

```
Call to undefined method App\Models\AccountPayable::dueToday()
```

**Error Location**: 
- `app/Services/ContasPagarService.php:78` - calling `AccountPayable::dueToday()`
- `app/Services/ContasPagarService.php:79` - calling `AccountPayable::dueThisWeek()`

## Root Cause
The `AccountPayable` model was incomplete. It only had basic fields and was missing:
1. Complete field definitions in `$fillable` array
2. Proper type casting in `$casts` array
3. Relationship methods (`supplier()`, `chartOfAccount()`)
4. Query scope methods (`scopeDueToday()`, `scopeDueThisWeek()`)

## Solution
Updated `/home/dg/projects/nexora-ems-erp/app/Models/AccountPayable.php` with:

### 1. Complete Fillable Fields
Added all fields matching the database migration:
```php
protected $fillable = [
    'description_title', 'supplier_id', 'chart_of_account_id',
    'amount', 'due_date_at', 'payment_date', 'paid_amount',
    'status', 'observation', 'attachment_path',
    'is_recurring', 'recurrence_day',
];
```

### 2. Proper Type Casting
```php
protected $casts = [
    'amount' => 'decimal:2',
    'paid_amount' => 'decimal:2',
    'due_date_at' => 'date',
    'payment_date' => 'date',
    'status' => PayableStatus::class,
    'is_recurring' => 'boolean',
    'recurrence_day' => 'integer',
];
```

### 3. Relationship Methods
```php
public function supplier(): BelongsTo
{
    return $this->belongsTo(Supplier::class);
}

public function chartOfAccount(): BelongsTo
{
    return $this->belongsTo(PlanOfAccount::class, 'chart_of_account_id');
}
```

### 4. Query Scope Methods (THE FIX)
```php
public function scopeDueToday(Builder $query): Builder
{
    return $query->where('due_date_at', today())
        ->whereIn('status', [PayableStatus::Pending->value, PayableStatus::Overdue->value]);
}

public function scopeDueThisWeek(Builder $query): Builder
{
    return $query->whereBetween('due_date_at', [today(), today()->addDays(7)])
        ->whereIn('status', [PayableStatus::Pending->value, PayableStatus::Overdue->value]);
}
```

## Impact
- ✅ Fixed the `BadMethodCallException` error
- ✅ Enabled KPI calculations in the Contas a Pagar module
- ✅ Properly configured model for eager loading relationships
- ✅ Added proper type safety with casts and enums

## Testing
The scope methods generate the expected SQL queries:
```sql
-- dueToday()
SELECT * FROM accounts_payable 
WHERE due_date_at = '2026-04-09' 
AND status IN ('pending', 'overdue')

-- dueThisWeek()
SELECT * FROM accounts_payable 
WHERE due_date_at BETWEEN '2026-04-09' AND '2026-04-16'
AND status IN ('pending', 'overdue')
```

## Related Files
- ✅ `/app/Models/AccountPayable.php` - **UPDATED**
- `/app/Services/ContasPagarService.php` - No changes needed
- `/app/Livewire/Financeiro/ContasPagar.php` - No changes needed
- `/app/Enums/PayableStatus.php` - Already exists

## Date Fixed
April 9, 2026

