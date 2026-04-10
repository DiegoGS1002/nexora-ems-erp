<?php

namespace App\Livewire\Financeiro;

use App\Enums\PayableStatus;
use App\Livewire\Forms\ContasPagarForm;
use App\Models\AccountPayable;
use App\Models\PlanOfAccount;
use App\Models\Supplier;
use App\Services\ContasPagarService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Contas a Pagar')]
class ContasPagar extends Component
{
    use WithPagination;

    /* ─────────────────────────────────────
      FORM OBJECT
     ─────────────────────────────────────*/
    public ContasPagarForm $form;

    /* ─────────────────────────────────────
      MODAL STATE
     ─────────────────────────────────────*/
    public bool  $showModal        = false;
    public bool  $isEditing        = false;
    public ?int  $editingId        = null;
    public bool  $showPaymentModal = false;
    public ?int  $payingId         = null;
    public bool  $showDeleteModal  = false;
    public ?int  $deletingId       = null;
    public bool  $showRescheduleModal = false;
    public ?int  $reschedulingId   = null;

    /* ─────────────────────────────────────
      PAYMENT FORM FIELDS
     ─────────────────────────────────────*/
    public string  $pay_date        = '';
    public string  $pay_amount      = '';
    public string  $pay_observation = '';

    /* ─────────────────────────────────────
      RESCHEDULE FORM
     ─────────────────────────────────────*/
    public string $reschedule_date = '';

    /* ─────────────────────────────────────
      FILTERS
     ─────────────────────────────────────*/
    public string $search        = '';
    public string $filterStatus  = '';
    public string $filterMonth   = '';

    protected $queryString = ['search', 'filterStatus'];

    /* ─────────────────────────────────────
      BOOT
     ─────────────────────────────────────*/
    public function boot(ContasPagarService $service): void
    {
        $service->syncOverdueStatus();
    }

    /* ─────────────────────────────────────
      COMPUTED — Accounts list (paginated)
     ─────────────────────────────────────*/
    #[Computed]
    public function accounts()
    {
        $query = AccountPayable::with(['supplier', 'chartOfAccount'])
            ->orderBy('due_date_at');

        if ($this->search !== '') {
            $q = '%' . $this->search . '%';
            $query->where(function ($qry) use ($q) {
                $qry->where('description_title', 'like', $q)
                    ->orWhereHas('supplier', fn ($s) => $s->where('name', 'like', $q)
                        ->orWhere('social_name', 'like', $q));
            });
        }

        if ($this->filterStatus !== '') {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterMonth !== '') {
            [$year, $month] = explode('-', $this->filterMonth);
            $query->whereYear('due_date_at', $year)->whereMonth('due_date_at', $month);
        }

        return $query->paginate(15);
    }

    /* ─────────────────────────────────────
      COMPUTED — KPIs
     ─────────────────────────────────────*/
    #[Computed]
    public function kpis(): array
    {
        return app(ContasPagarService::class)->getKpiData();
    }

    /* ─────────────────────────────────────
      COMPUTED — Selects
     ─────────────────────────────────────*/
    #[Computed]
    public function suppliers()
    {
        return Supplier::orderBy('social_name')->get(['id', 'name', 'social_name']);
    }

    #[Computed]
    public function chartAccounts()
    {
        return PlanOfAccount::where('is_active', true)
            ->where('is_selectable', true)
            ->where('type', 'despesa')
            ->orderBy('code')
            ->get(['id', 'code', 'name']);
    }

    /* ─────────────────────────────────────
      MODAL OPEN — CREATE
     ─────────────────────────────────────*/
    public function openCreate(): void
    {
        $this->form->reset();
        $this->form->status    = PayableStatus::Pending->value;
        $this->form->due_date_at = today()->format('Y-m-d');
        $this->isEditing       = false;
        $this->editingId       = null;
        $this->showModal       = true;
    }

    /* ─────────────────────────────────────
      MODAL OPEN — EDIT
     ─────────────────────────────────────*/
    public function openEdit(int $id): void
    {
        $account = AccountPayable::findOrFail($id);

        $this->form->fill([
            'description_title'   => $account->description_title ?? '',
            'supplier_id'         => $account->supplier_id,
            'chart_of_account_id' => $account->chart_of_account_id,
            'amount'              => (string) $account->amount,
            'due_date_at'         => $account->due_date_at?->format('Y-m-d') ?? '',
            'status'              => $account->status->value ?? PayableStatus::Pending->value,
            'observation'         => $account->observation,
            'is_recurring'        => $account->is_recurring,
            'recurrence_day'      => $account->recurrence_day,
        ]);

        $this->editingId = $id;
        $this->isEditing = true;
        $this->showModal = true;
    }

    /* ─────────────────────────────────────
      SAVE
     ─────────────────────────────────────*/
    public function save(ContasPagarService $service): void
    {
        $this->form->validate();

        $data = [
            'description_title'   => $this->form->description_title,
            'supplier_id'         => $this->form->supplier_id ?: null,
            'chart_of_account_id' => $this->form->chart_of_account_id ?: null,
            'amount'              => (float) str_replace(['.', ','], ['', '.'], $this->form->amount),
            'due_date_at'         => $this->form->due_date_at,
            'status'              => $this->form->status,
            'observation'         => $this->form->observation ?: null,
            'is_recurring'        => $this->form->is_recurring,
            'recurrence_day'      => $this->form->is_recurring ? $this->form->recurrence_day : null,
        ];

        if ($this->isEditing) {
            $service->update(AccountPayable::findOrFail($this->editingId), $data);
            session()->flash('success', 'Conta a pagar atualizada com sucesso!');
        } else {
            $service->create($data);
            session()->flash('success', 'Conta a pagar cadastrada com sucesso!');
        }

        $this->closeModal();
    }

    /* ─────────────────────────────────────
      PAYMENT MODAL (Baixa)
     ─────────────────────────────────────*/
    public function openPayment(int $id): void
    {
        $account = AccountPayable::findOrFail($id);

        $this->payingId        = $id;
        $this->pay_date        = today()->format('Y-m-d');
        $this->pay_amount      = (string) $account->amount;
        $this->pay_observation = '';
        $this->showPaymentModal = true;
    }

    public function registerPayment(ContasPagarService $service): void
    {
        $this->validate([
            'pay_date'   => 'required|date',
            'pay_amount' => 'required|numeric|min:0.01',
        ], [
            'pay_date.required'   => 'Informe a data do pagamento.',
            'pay_amount.required' => 'Informe o valor pago.',
            'pay_amount.min'      => 'O valor pago deve ser maior que zero.',
        ]);

        $service->registerPayment(
            AccountPayable::findOrFail($this->payingId),
            $this->pay_date,
            (float) str_replace(['.', ','], ['', '.'], $this->pay_amount),
            $this->pay_observation ?: null
        );

        session()->flash('success', 'Pagamento registrado com sucesso!');
        $this->closePaymentModal();
    }

    public function closePaymentModal(): void
    {
        $this->showPaymentModal = false;
        $this->payingId        = null;
        $this->pay_date        = '';
        $this->pay_amount      = '';
        $this->pay_observation = '';
        $this->resetErrorBag(['pay_date', 'pay_amount', 'pay_observation']);
    }

    /* ─────────────────────────────────────
      RESCHEDULE MODAL
     ─────────────────────────────────────*/
    public function openReschedule(int $id): void
    {
        $account = AccountPayable::findOrFail($id);

        $this->reschedulingId   = $id;
        $this->reschedule_date  = $account->due_date_at?->format('Y-m-d') ?? '';
        $this->showRescheduleModal = true;
    }

    public function reschedule(ContasPagarService $service): void
    {
        $this->validate([
            'reschedule_date' => 'required|date',
        ], [
            'reschedule_date.required' => 'Informe a nova data de vencimento.',
        ]);

        $service->reschedule(
            AccountPayable::findOrFail($this->reschedulingId),
            $this->reschedule_date
        );

        session()->flash('success', 'Vencimento reprogramado com sucesso!');
        $this->closeRescheduleModal();
    }

    public function closeRescheduleModal(): void
    {
        $this->showRescheduleModal = false;
        $this->reschedulingId      = null;
        $this->reschedule_date     = '';
        $this->resetErrorBag(['reschedule_date']);
    }

    /* ─────────────────────────────────────
      DELETE CONFIRMATION
     ─────────────────────────────────────*/
    public function confirmDelete(int $id): void
    {
        $this->deletingId      = $id;
        $this->showDeleteModal = true;
    }

    public function delete(ContasPagarService $service): void
    {
        if ($this->deletingId) {
            $service->delete(AccountPayable::findOrFail($this->deletingId));
            session()->flash('success', 'Conta a pagar excluída com sucesso!');
        }

        $this->showDeleteModal = false;
        $this->deletingId      = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingId      = null;
    }

    /* ─────────────────────────────────────
      CANCEL ACCOUNT
     ─────────────────────────────────────*/
    public function cancelAccount(int $id, ContasPagarService $service): void
    {
        $service->cancel(AccountPayable::findOrFail($id));
        session()->flash('success', 'Conta cancelada.');
    }

    /* ─────────────────────────────────────
      CLOSE MODAL
     ─────────────────────────────────────*/
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->isEditing = false;
        $this->editingId = null;
        $this->form->reset();
    }

    /* ─────────────────────────────────────
      RESET PAGINATION ON FILTER CHANGE
     ─────────────────────────────────────*/
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterMonth(): void
    {
        $this->resetPage();
    }

    /* ─────────────────────────────────────
      RENDER
     ─────────────────────────────────────*/
    public function render(): View
    {
        return view('livewire.financeiro.contas-pagar.index', [
            'accounts'     => $this->accounts,
            'kpis'         => $this->kpis,
            'suppliers'    => $this->suppliers,
            'chartAccounts'=> $this->chartAccounts,
            'statuses'     => PayableStatus::cases(),
        ]);
    }
}

