<?php

namespace App\Services;

use App\Enums\PayrollStatus;
use App\Enums\PayableStatus;
use App\Models\AccountPayable;
use App\Models\Employees;
use App\Models\Payroll;
use App\Models\PayrollItem;
use Carbon\Carbon;

class PayrollService
{
    /**
     * Generate payroll for a single employee
     */
    public function generateForEmployee(string $employeeId, Carbon $referenceDate): Payroll
    {
        $employee = Employees::findOrFail($employeeId);

        return Payroll::firstOrCreate(
            [
                'employee_id' => $employeeId,
                'reference_month' => $referenceDate->startOfMonth()->format('Y-m-d'),
            ],
            [
                'base_salary' => $employee->salary ?? 0,
                'total_earnings' => 0,
                'total_deductions' => 0,
                'net_salary' => $employee->salary ?? 0,
                'status' => PayrollStatus::Draft->value,
            ]
        );
    }

    /**
     * Generate payrolls for all active employees
     */
    public function generateForAllEmployees(Carbon $referenceDate): int
    {
        $employees = Employees::where('is_active', true)->get();
        $count = 0;

        foreach ($employees as $employee) {
            $exists = Payroll::where('employee_id', $employee->id)
                ->whereYear('reference_month', $referenceDate->year)
                ->whereMonth('reference_month', $referenceDate->month)
                ->exists();

            if (!$exists) {
                $this->generateForEmployee($employee->id, $referenceDate);
                $count++;
            }
        }

        return $count;
    }

    /**
     * Add or update payroll item
     */
    public function saveItem(
        int $payrollId,
        string $description,
        string $type,
        float $amount,
        ?int $itemId = null
    ): PayrollItem {
        if ($itemId) {
            $item = PayrollItem::findOrFail($itemId);
            $item->update([
                'description' => $description,
                'type' => $type,
                'amount' => $amount,
            ]);
        } else {
            $item = PayrollItem::create([
                'payroll_id' => $payrollId,
                'description' => $description,
                'type' => $type,
                'amount' => $amount,
            ]);
        }

        $payroll = Payroll::findOrFail($payrollId);
        $payroll->recalculate();

        return $item;
    }

    /**
     * Remove payroll item
     */
    public function removeItem(int $itemId): void
    {
        $item = PayrollItem::findOrFail($itemId);
        $payrollId = $item->payroll_id;

        $item->delete();

        $payroll = Payroll::findOrFail($payrollId);
        $payroll->recalculate();
    }

    /**
     * Close payroll (change status to Closed)
     */
    public function closePayroll(int $payrollId): Payroll
    {
        $payroll = Payroll::findOrFail($payrollId);
        $payroll->update(['status' => PayrollStatus::Closed->value]);

        return $payroll;
    }

    /**
     * Mark payroll as paid
     */
    public function markAsPaid(int $payrollId, Carbon $paymentDate): Payroll
    {
        $payroll = Payroll::with('employee')->findOrFail($payrollId);
        $payroll->update([
            'status'       => PayrollStatus::Paid->value,
            'payment_date' => $paymentDate,
        ]);

        // Financeiro — registrar conta a pagar quitada (histórico de pagamento de folha)
        AccountPayable::create([
            'description_title' => 'Folha de Pagamento - ' . ($payroll->employee->name ?? 'Funcionário') . ' (' . $payroll->reference_month->format('m/Y') . ')',
            'amount'            => $payroll->net_salary,
            'paid_amount'       => $payroll->net_salary,
            'due_date_at'       => $paymentDate,
            'payment_date'      => $paymentDate,
            'status'            => PayableStatus::Paid->value,
            'observation'       => 'Pagamento de folha gerado automaticamente.',
        ]);

        return $payroll;
    }

    /**
     * Delete payroll and all its items
     */
    public function deletePayroll(int $payrollId): void
    {
        $payroll = Payroll::findOrFail($payrollId);
        $payroll->items()->delete();
        $payroll->delete();
    }

    /**
     * Calculate KPIs for a specific month
     */
    public function getKPIs(Carbon $referenceDate): array
    {
        $payrolls = Payroll::whereYear('reference_month', $referenceDate->year)
            ->whereMonth('reference_month', $referenceDate->month)
            ->get();

        return [
            'total_earnings' => $payrolls->sum('total_earnings'),
            'total_deductions' => $payrolls->sum('total_deductions'),
            'net_salary' => $payrolls->sum('net_salary'),
            'count_draft' => $payrolls->where('status', PayrollStatus::Draft)->count(),
            'count_closed' => $payrolls->where('status', PayrollStatus::Closed)->count(),
            'count_paid' => $payrolls->where('status', PayrollStatus::Paid)->count(),
        ];
    }

    /**
     * Get payrolls with employees for a specific month
     */
    public function getPayrollsForMonth(Carbon $referenceDate): \Illuminate\Support\Collection
    {
        $employees = Employees::where('is_active', true)->orderBy('name')->get();

        $payrolls = Payroll::with(['items'])
            ->whereYear('reference_month', $referenceDate->year)
            ->whereMonth('reference_month', $referenceDate->month)
            ->get()
            ->keyBy('employee_id');

        return $employees->map(fn($emp) => [
            'employee' => $emp,
            'payroll' => $payrolls->get($emp->id),
        ]);
    }
}

