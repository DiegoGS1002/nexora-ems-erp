<?php

namespace App\Services;

use App\Enums\PayableStatus;
use App\Models\AccountPayable;
use Illuminate\Support\Carbon;

class ContasPagarService
{
    public function create(array $data): AccountPayable
    {
        return AccountPayable::create($data);
    }

    public function update(AccountPayable $account, array $data): bool
    {
        return $account->update($data);
    }

    public function delete(AccountPayable $account): bool
    {
        return $account->delete();
    }

    /**
     * Registers payment (Baixa) for an account payable.
     * Applies interest/discounts and marks status as Paid.
     */
    public function registerPayment(
        AccountPayable $account,
        string $paymentDate,
        float $paidAmount,
        ?string $observation = null
    ): void {
        $account->update([
            'status'       => PayableStatus::Paid,
            'payment_date' => Carbon::parse($paymentDate),
            'paid_amount'  => $paidAmount,
            'observation'  => $observation ?? $account->observation,
        ]);
    }

    /**
     * Reschedule a payable account to a new due date.
     */
    public function reschedule(AccountPayable $account, string $newDueDate): void
    {
        $account->update([
            'due_date_at' => Carbon::parse($newDueDate),
            'status'      => PayableStatus::Pending,
        ]);
    }

    /**
     * Cancel an account payable.
     */
    public function cancel(AccountPayable $account): void
    {
        $account->update(['status' => PayableStatus::Cancelled]);
    }

    /**
     * Sync overdue status for all pending accounts past their due date.
     */
    public function syncOverdueStatus(): int
    {
        return AccountPayable::where('status', PayableStatus::Pending->value)
            ->where('due_date_at', '<', today())
            ->update(['status' => PayableStatus::Overdue->value]);
    }

    /**
     * Calculate KPI summary data.
     */
    public function getKpiData(): array
    {
        $dueToday = AccountPayable::dueToday()->sum('amount');
        $dueWeek  = AccountPayable::dueThisWeek()->sum('amount');

        $totalPending = AccountPayable::whereIn('status', [
            PayableStatus::Pending->value,
            PayableStatus::Overdue->value,
        ])->sum('amount');

        $totalPaid = AccountPayable::where('status', PayableStatus::Paid->value)
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('paid_amount');

        $countOverdue = AccountPayable::where('status', PayableStatus::Overdue->value)->count();

        return compact('dueToday', 'dueWeek', 'totalPending', 'totalPaid', 'countOverdue');
    }
}

