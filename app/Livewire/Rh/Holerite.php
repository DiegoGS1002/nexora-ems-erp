<?php

namespace App\Livewire\Rh;

use App\Enums\PayrollStatus;
use App\Models\Employees;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Holerite')]
class Holerite extends Component
{
    /* ─────────────────────────────────────
      FILTROS
     ─────────────────────────────────────*/
    public string $referenceMonth = '';
    public string $searchEmployee  = '';
    public string $filterStatus    = '';

    /* ─────────────────────────────────────
      SELEÇÃO
     ─────────────────────────────────────*/
    public ?int $selectedPayrollId = null;

    /* ─────────────────────────────────────
      MODAL — FECHAR FOLHA
     ─────────────────────────────────────*/
    public bool $showCloseModal = false;
    public ?int $closingId      = null;

    /* ─────────────────────────────────────
      MODAL — MARCAR PAGO
     ─────────────────────────────────────*/
    public bool   $showPaidModal = false;
    public ?int   $payingId      = null;
    public string $paymentDate   = '';

    /* ─────────────────────────────────────
      ITEM FORM (edição de verbas)
     ─────────────────────────────────────*/
    public bool    $showItemForm    = false;
    public ?int    $editingItemId   = null;
    public string  $itemDescription = '';
    public string  $itemType        = 'earning';
    public string  $itemAmount      = '';

    /* ─────────────────────────────────────
      BOOT
     ─────────────────────────────────────*/
    public function mount(): void
    {
        $this->referenceMonth = now()->format('Y-m');
        $this->paymentDate    = now()->format('Y-m-d');
    }

    /* ─────────────────────────────────────
      COMPUTED — lista lateral de folhas
     ─────────────────────────────────────*/
    #[Computed]
    public function payrollList(): \Illuminate\Support\Collection
    {
        $date = $this->refDate();

        $query = Payroll::with(['employee'])
            ->whereYear('reference_month', $date->year)
            ->whereMonth('reference_month', $date->month);

        if ($this->filterStatus !== '') {
            $query->where('status', $this->filterStatus);
        }

        $payrolls = $query->get();

        if ($this->searchEmployee !== '') {
            $term     = mb_strtolower($this->searchEmployee);
            $payrolls = $payrolls->filter(function ($p) use ($term) {
                return str_contains(mb_strtolower($p->employee?->name ?? ''), $term);
            });
        }

        return $payrolls->sortBy(fn($p) => $p->employee?->name ?? '')->values();
    }

    /* ─────────────────────────────────────
      COMPUTED — holerite selecionado
     ─────────────────────────────────────*/
    #[Computed]
    public function selectedPayroll(): ?Payroll
    {
        if (! $this->selectedPayrollId) {
            return null;
        }

        return Payroll::with(['items', 'employee'])->find($this->selectedPayrollId);
    }

    /* ─────────────────────────────────────
      COMPUTED — KPIs do mês
     ─────────────────────────────────────*/
    #[Computed]
    public function kpis(): array
    {
        $date     = $this->refDate();
        $payrolls = Payroll::whereYear('reference_month', $date->year)
            ->whereMonth('reference_month', $date->month)
            ->get();

        return [
            'count_total'      => $payrolls->count(),
            'count_draft'      => $payrolls->where('status', PayrollStatus::Draft)->count(),
            'count_closed'     => $payrolls->where('status', PayrollStatus::Closed)->count(),
            'count_paid'       => $payrolls->where('status', PayrollStatus::Paid)->count(),
            'total_net_salary' => $payrolls->sum('net_salary'),
        ];
    }

    /* ─────────────────────────────────────
      COMPUTED — dados da empresa (settings)
     ─────────────────────────────────────*/
    #[Computed]
    public function companyData(): array
    {
        return [
            'name'   => Setting::get('company_name', 'Nexora ERP'),
            'cnpj'   => Setting::get('company_cnpj', '00.000.000/0001-00'),
            'address'=> Setting::get('company_address', ''),
            'city'   => Setting::get('company_city', ''),
            'state'  => Setting::get('company_state', ''),
        ];
    }

    /* ─────────────────────────────────────
      SELECIONAR HOLERITE
     ─────────────────────────────────────*/
    public function selectPayroll(int $id): void
    {
        $this->selectedPayrollId = $id;
        $this->showItemForm       = false;
        $this->editingItemId      = null;
        $this->resetItemForm();
        unset($this->selectedPayroll);
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
            $this->selectedPayrollId,
            $this->itemDescription,
            $this->itemType,
            $amount,
            $this->editingItemId
        );

        $this->showItemForm  = false;
        $this->editingItemId = null;
        $this->resetItemForm();
        unset($this->selectedPayroll);
    }

    public function removeItem(int $itemId, \App\Services\PayrollService $service): void
    {
        $service->removeItem($itemId);
        unset($this->selectedPayroll);
    }

    private function resetItemForm(): void
    {
        $this->itemDescription = '';
        $this->itemType        = 'earning';
        $this->itemAmount      = '';
        $this->resetErrorBag(['itemDescription', 'itemType', 'itemAmount']);
    }

    /* ─────────────────────────────────────
      FECHAR FOLHA
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
        unset($this->selectedPayroll, $this->payrollList, $this->kpis);
    }

    /* ─────────────────────────────────────
      MARCAR COMO PAGO
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
        unset($this->selectedPayroll, $this->payrollList, $this->kpis);
    }

    /* ─────────────────────────────────────
      RESET DE FILTROS
     ─────────────────────────────────────*/
    public function updatingReferenceMonth(): void
    {
        $this->selectedPayrollId = null;
        $this->showItemForm       = false;
        unset($this->payrollList, $this->kpis, $this->selectedPayroll);
    }

    public function updatingSearchEmployee(): void
    {
        unset($this->payrollList);
    }

    public function updatingFilterStatus(): void
    {
        unset($this->payrollList);
    }

    /* ─────────────────────────────────────
      HELPER
     ─────────────────────────────────────*/
    private function refDate(): \Carbon\Carbon
    {
        return \Carbon\Carbon::createFromFormat('Y-m', $this->referenceMonth)->startOfMonth();
    }

    /* ─────────────────────────────────────
      RENDER
     ─────────────────────────────────────*/
    public function render(): View
    {
        return view('livewire.rh.holerite.index', [
            'payrollList'     => $this->payrollList,
            'selectedPayroll' => $this->selectedPayroll,
            'kpis'            => $this->kpis,
            'companyData'     => $this->companyData,
        ]);
    }
}

