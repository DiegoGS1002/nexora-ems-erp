<?php

namespace App\Services;

use App\Enums\ReceivableStatus;
use App\Models\AccountReceivable;
use Illuminate\Support\Carbon;

class ContasReceberService
{
    public function create(array $data): AccountReceivable
    {
        return AccountReceivable::create($data);
    }

    public function update(AccountReceivable $account, array $data): bool
    {
        return $account->update($data);
    }

    public function delete(AccountReceivable $account): bool
    {
        return $account->delete();
    }

    public function registerReceipt(
        AccountReceivable $account,
        string $receivedAt,
        float $receivedAmount,
        ?string $observation = null
    ): void {
        $status = $receivedAmount >= (float) $account->amount
            ? ReceivableStatus::Received
            : ReceivableStatus::Partial;

        $account->update([
            'status'          => $status,
            'received_at'     => Carbon::parse($receivedAt),
            'received_amount' => $receivedAmount,
            'observation'     => $observation ?? $account->observation,
        ]);
    }

    public function reschedule(AccountReceivable $account, string $newDueDate): void
    {
        $account->update([
            'due_date_at' => Carbon::parse($newDueDate),
            'status'      => ReceivableStatus::Pending,
        ]);
    }

    public function cancel(AccountReceivable $account): void
    {
        $account->update(['status' => ReceivableStatus::Cancelled]);
    }

    public function syncOverdueStatus(): int
    {
        return AccountReceivable::where('status', ReceivableStatus::Pending->value)
            ->where('due_date_at', '<', today())
            ->update(['status' => ReceivableStatus::Overdue->value]);
    }

    public function getKpiData(): array
    {
        $totalPending = AccountReceivable::whereIn('status', [
            ReceivableStatus::Pending->value,
            ReceivableStatus::Overdue->value,
            ReceivableStatus::Partial->value,
        ])->sum('amount');

        $receivedToday = AccountReceivable::where('received_at', today())
            ->whereIn('status', [ReceivableStatus::Received->value, ReceivableStatus::Partial->value])
            ->sum('received_amount');

        $overdueTotal = AccountReceivable::where('status', ReceivableStatus::Overdue->value)
            ->sum('amount');

        $countOverdue = AccountReceivable::where('status', ReceivableStatus::Overdue->value)->count();

        $receivedThisMonth = AccountReceivable::whereIn('status', [
            ReceivableStatus::Received->value,
            ReceivableStatus::Partial->value,
        ])
            ->whereMonth('received_at', now()->month)
            ->whereYear('received_at', now()->year)
            ->sum('received_amount');

        return compact('totalPending', 'receivedToday', 'overdueTotal', 'countOverdue', 'receivedThisMonth');
    }
}

