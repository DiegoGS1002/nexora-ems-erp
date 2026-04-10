<?php

namespace App\Livewire\Rh;

use App\Enums\PayrollStatus;
use App\Models\Employees;
use App\Models\Payroll;
use App\Models\PayrollItem;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Folha de Pagamento')]
class FolhaPagamento extends Component
{
    /* ─────────────────────────────────────
      FILTERS
     ─────────────────────────────────────*/
    public string $referenceMonth = '';

    /* ─────────────────────────────────────
      MODAL STATE — HOLERITE
     ─────────────────────────────────────*/
    public bool    $showHolerite    = false;
    public ?int    $holeriteId      = null;
    public string  $itemDescription = '';
    public string  $itemType        = 'earning';
    public string  $itemAmount      = '';
    public ?int    $editingItemId   = null;
    public bool    $showItemForm    = false;

    /* ─────────────────────────────────────
      MODAL STATE — FECHAR FOLHA
     ─────────────────────────────────────*/
    public bool  $showCloseModal = false;
    public ?int  $closingId      = null;

    /* ─────────────────────────────────────
      MODAL STATE — MARCAR PAGO
     ─────────────────────────────────────*/
    public bool   $showPaidModal = false;
    public ?int   $payingId      = null;
    public string $paymentDate   = '';

    /* ─────────────────────────────────────
      MODAL STATE — GENERATE ALL
     ─────────────────────────────────────*/
    public bool $showGenerateAllModal = false;

    /* ─────────────────────────────────────
      BOOT
     ─────────────────────────────────────*/
    public function mount(): void
    {
        $this->referenceMonth = now()->format('Y-m');
        $this->paymentDate    = now()->format('Y-m-d');
    }

    /* ─────────────────────────────────────
      HELPERS — Reference date
     ─────────────────────────────────────*/
    private function refDate(): \Carbon\Carbon
    {
        return \Carbon\Carbon::createFromFormat('Y-m', $this->referenceMonth)->startOfMonth();
    }

    /* ─────────────────────────────────────
      COMPUTED — Employees with payroll data for the month
     ─────────────────────────────────────*/
    #[Computed]
    public function rows(): \Illuminate\Support\Collection
    {
        $date      = $this->refDate();
        $employees = Employees::where('is_active', true)->orderBy('name')->get();

        $payrolls = Payroll::with(['items'])
            ->whereYear('reference_month', $date->year)
            ->whereMonth('reference_month', $date->month)
            ->get()
            ->keyBy('employee_id');

        return $employees->map(fn($emp) => [
            'employee' => $emp,
            'payroll'  => $payrolls->get($emp->id),
        ]);
    }

    /* ─────────────────────────────────────
      COMPUTED — KPIs
     ─────────────────────────────────────*/
    #[Computed]
    public function kpis(): array
    {
        $date = $this->refDate();

        $payrolls = Payroll::whereYear('reference_month', $date->year)
            ->whereMonth('reference_month', $date->month)
            ->get();

        return [
            'total_earnings'   => $payrolls->sum('total_earnings'),
            'total_deductions' => $payrolls->sum('total_deductions'),
            'net_salary'       => $payrolls->sum('net_salary'),
            'count_draft'      => $payrolls->where('status', PayrollStatus::Draft)->count(),
            'count_closed'     => $payrolls->where('status', PayrollStatus::Closed)->count(),
            'count_paid'       => $payrolls->where('status', PayrollStatus::Paid)->count(),
        ];
    }

    /* ─────────────────────────────────────
      COMPUTED — Current Holerite
     ─────────────────────────────────────*/
    #[Computed]
    public function currentPayroll(): ?Payroll
    {
        if (! $this->holeriteId) return null;
        return Payroll::with(['employee', 'items'])->find($this->holeriteId);
    }

    /* ─────────────────────────────────────
      GENERATE — Single employee
     ─────────────────────────────────────*/
    public function generatePayroll(string $employeeId): void
    {
        $employee = Employees::findOrFail($employeeId);
        $date     = $this->refDate();

        $payroll = Payroll::firstOrCreate(
            [
                'employee_id'     => $employeeId,
                'reference_month' => $date->format('Y-m-d'),
            ],
            [
                'base_salary'      => $employee->salary ?? 0,
                'total_earnings'   => 0,
                'total_deductions' => 0,
                'net_salary'       => $employee->salary ?? 0,
                'status'           => PayrollStatus::Draft->value,
            ]
        );

        session()->flash('success', "Folha gerada para {$employee->name}.");

        $this->openHolerite($payroll->id);
    }

    /* ─────────────────────────────────────
      GENERATE ALL — All active employees
     ─────────────────────────────────────*/
    public function generateAllPayrolls(): void
    {
        $date      = $this->refDate();
        $employees = Employees::where('is_active', true)->get();
        $count     = 0;

        foreach ($employees as $emp) {
            $already = Payroll::where('employee_id', $emp->id)
                ->whereYear('reference_month', $date->year)
                ->whereMonth('reference_month', $date->month)
                ->exists();

            if (! $already) {
                Payroll::create([
                    'employee_id'      => $emp->id,
                    'reference_month'  => $date->format('Y-m-d'),
                    'base_salary'      => $emp->salary ?? 0,
                    'total_earnings'   => 0,
                    'total_deductions' => 0,
                    'net_salary'       => $emp->salary ?? 0,
                    'status'           => PayrollStatus::Draft->value,
                ]);
                $count++;
            }
        }

        $this->showGenerateAllModal = false;
        session()->flash('success', "{$count} folha(s) gerada(s) para " . now()->setMonth($date->month)->translatedFormat('F/Y') . '.');
    }

    /* ─────────────────────────────────────
      HOLERITE MODAL
     ─────────────────────────────────────*/
    public function openHolerite(int $id): void
    {
        $this->holeriteId    = $id;
        $this->showHolerite  = true;
        $this->showItemForm  = false;
        $this->resetItemForm();
    }

    public function closeHolerite(): void
    {
        $this->showHolerite   = false;
        $this->holeriteId     = null;
        $this->editingItemId  = null;
        $this->showItemForm   = false;
        $this->resetItemForm();
    }

    /* ─────────────────────────────────────
      ITEM FORM
     ─────────────────────────────────────*/
    public function openItemForm(?int $itemId = null): void
    {
        $this->editingItemId = $itemId;
        $this->showItemForm  = true;

        if ($itemId) {
            $item = PayrollItem::findOrFail($itemId);
            $this->itemDescription = $item->description;
            $this->itemType        = $item->type;
            $this->itemAmount      = number_format((float) $item->amount, 2, ',', '.');
        } else {
            $this->resetItemForm();
        }
    }

    public function saveItem(): void
    {
        $this->validate([
            'itemDescription' => 'required|string|max:255',
            'itemType'        => 'required|in:earning,deduction',
            'itemAmount'      => 'required',
        ], [
            'itemDescription.required' => 'Informe a descrição.',
            'itemType.required'        => 'Selecione o tipo.',
            'itemAmount.required'      => 'Informe o valor.',
        ]);

        $amount = (float) str_replace(['.', ','], ['', '.'], $this->itemAmount);

        if ($this->editingItemId) {
            PayrollItem::findOrFail($this->editingItemId)->update([
                'description' => $this->itemDescription,
                'type'        => $this->itemType,
                'amount'      => $amount,
            ]);
        } else {
            PayrollItem::create([
                'payroll_id'  => $this->holeriteId,
                'description' => $this->itemDescription,
                'type'        => $this->itemType,
                'amount'      => $amount,
            ]);
        }

        // Recalculate totals
        $payroll = Payroll::findOrFail($this->holeriteId);
        $payroll->recalculate();

        $this->showItemForm  = false;
        $this->editingItemId = null;
        $this->resetItemForm();
        unset($this->currentPayroll);
    }

    public function removeItem(int $itemId): void
    {
        PayrollItem::findOrFail($itemId)->delete();

        $payroll = Payroll::findOrFail($this->holeriteId);
        $payroll->recalculate();

        unset($this->currentPayroll);
    }

    private function resetItemForm(): void
    {
        $this->itemDescription = '';
        $this->itemType        = 'earning';
        $this->itemAmount      = '';
        $this->resetErrorBag(['itemDescription', 'itemType', 'itemAmount']);
    }

    /* ─────────────────────────────────────
      CLOSE PAYROLL (draft → closed)
     ─────────────────────────────────────*/
    public function openCloseModal(int $id): void
    {
        $this->closingId      = $id;
        $this->showCloseModal = true;
    }

    public function closePayroll(): void
    {
        if ($this->closingId) {
            Payroll::findOrFail($this->closingId)->update(['status' => PayrollStatus::Closed->value]);
            session()->flash('success', 'Folha fechada com sucesso!');
        }

        $this->showCloseModal = false;
        $this->closingId      = null;
        unset($this->rows, $this->kpis);
    }

    /* ─────────────────────────────────────
      MARK AS PAID (closed → paid)
     ─────────────────────────────────────*/
    public function openPaidModal(int $id): void
    {
        $this->payingId      = $id;
        $this->paymentDate   = now()->format('Y-m-d');
        $this->showPaidModal = true;
    }

    public function markAsPaid(): void
    {
        $this->validate([
            'paymentDate' => 'required|date',
        ], [
            'paymentDate.required' => 'Informe a data de pagamento.',
        ]);

        if ($this->payingId) {
            Payroll::findOrFail($this->payingId)->update([
                'status'       => PayrollStatus::Paid->value,
                'payment_date' => $this->paymentDate,
            ]);
            session()->flash('success', 'Salário marcado como pago!');
        }

        $this->showPaidModal = false;
        $this->payingId      = null;
        unset($this->rows, $this->kpis);
    }

    /* ─────────────────────────────────────
      DELETE PAYROLL
     ─────────────────────────────────────*/
    public function deletePayroll(int $id): void
    {
        Payroll::findOrFail($id)->delete();
        session()->flash('success', 'Registro removido.');
        unset($this->rows, $this->kpis);
    }

    /* ─────────────────────────────────────
      RESET PAGINATION ON FILTER CHANGE
     ─────────────────────────────────────*/
    public function updatingReferenceMonth(): void
    {
        unset($this->rows, $this->kpis);
    }

    /* ─────────────────────────────────────
      RENDER
     ─────────────────────────────────────*/
    public function render(): View
    {
        return view('livewire.rh.folha-pagamento.index', [
            'rows'   => $this->rows,
            'kpis'   => $this->kpis,
            'statuses' => PayrollStatus::cases(),
        ]);
    }
}

