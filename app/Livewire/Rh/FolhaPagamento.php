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
      GENERATE SINGLE — One employee
     ─────────────────────────────────────*/
    public function generatePayroll(string $employeeId, \App\Services\PayrollService $service): void
    {
        $payroll = $service->generateForEmployee($employeeId, $this->refDate());

        unset($this->rows, $this->kpis);

        $this->openHolerite($payroll->id);
    }

    /* ─────────────────────────────────────
      GENERATE ALL — All active employees
     ─────────────────────────────────────*/
    public function generateAllPayrolls(\App\Services\PayrollService $service): void
    {
        $date  = $this->refDate();
        $count = $service->generateForAllEmployees($date);

        $this->showGenerateAllModal = false;
        unset($this->rows, $this->kpis);

        session()->flash('success', "Folha gerada para {$count} funcionário(s).");
    }

    /* ─────────────────────────────────────
      HOLERITE MODAL
     ─────────────────────────────────────*/
    public function openHolerite(int $id): void
    {
        $this->holeriteId   = $id;
        $this->showHolerite = true;
        unset($this->currentPayroll);
    }

    public function closeHolerite(): void
    {
        $this->showHolerite  = false;
        $this->holeriteId    = null;
        $this->showItemForm  = false;
        $this->editingItemId = null;
        $this->resetItemForm();
        unset($this->currentPayroll);
    }

    /* ─────────────────────────────────────
      ITEM FORM
     ─────────────────────────────────────*/
    public function openItemForm(?int $itemId = null): void
    {
        $this->editingItemId = $itemId;

        if ($itemId) {
            $item                  = PayrollItem::findOrFail($itemId);
            $this->itemDescription = $item->description;
            $this->itemType        = $item->type;
            $this->itemAmount      = (string) $item->amount;
        } else {
            $this->resetItemForm();
        }

        $this->showItemForm = true;
    }

    public function saveItem(\App\Services\PayrollService $service): void
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

        $service->saveItem(
            $this->holeriteId,
            $this->itemDescription,
            $this->itemType,
            $amount,
            $this->editingItemId
        );

        $this->showItemForm  = false;
        $this->editingItemId = null;
        $this->resetItemForm();
        unset($this->currentPayroll);
    }

    public function removeItem(int $itemId, \App\Services\PayrollService $service): void
    {
        $service->removeItem($itemId);
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

    public function closePayroll(\App\Services\PayrollService $service): void
    {
        if ($this->closingId) {
            $service->closePayroll($this->closingId);
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

    public function markAsPaid(\App\Services\PayrollService $service): void
    {
        $this->validate([
            'paymentDate' => 'required|date',
        ], [
            'paymentDate.required' => 'Informe a data de pagamento.',
        ]);

        if ($this->payingId) {
            $service->markAsPaid(
                $this->payingId,
                \Carbon\Carbon::parse($this->paymentDate)
            );
            session()->flash('success', 'Salário marcado como pago!');
        }

        $this->showPaidModal = false;
        $this->payingId      = null;
        unset($this->rows, $this->kpis);
    }

    /* ─────────────────────────────────────
      DELETE PAYROLL
     ─────────────────────────────────────*/
    public function deletePayroll(int $id, \App\Services\PayrollService $service): void
    {
        $service->deletePayroll($id);
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

