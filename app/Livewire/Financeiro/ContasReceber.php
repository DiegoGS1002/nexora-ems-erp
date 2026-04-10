<?php

namespace App\Livewire\Financeiro;

use App\Enums\ReceivableStatus;
use App\Livewire\Forms\ContasReceberForm;
use App\Models\AccountReceivable;
use App\Models\Client;
use App\Models\PlanOfAccount;
use App\Services\ContasReceberService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Contas a Receber')]
class ContasReceber extends Component
{
    use WithPagination;

    /* ─────────────────────────────────────
      FORM OBJECT
     ─────────────────────────────────────*/
    public ContasReceberForm $form;

    /* ─────────────────────────────────────
      MODAL STATE
     ─────────────────────────────────────*/
    public bool  $showModal          = false;
    public bool  $isEditing          = false;
    public ?int  $editingId          = null;
    public bool  $showReceiptModal   = false;
    public ?int  $receivingId        = null;
    public bool  $showDeleteModal    = false;
    public ?int  $deletingId         = null;
    public bool  $showRescheduleModal = false;
    public ?int  $reschedulingId     = null;

    /* ─────────────────────────────────────
      RECEIPT FORM FIELDS
     ─────────────────────────────────────*/
    public string $receipt_date        = '';
    public string $receipt_amount      = '';
    public string $receipt_observation = '';

    /* ─────────────────────────────────────
      RESCHEDULE FORM
     ─────────────────────────────────────*/
    public string $reschedule_date = '';

    /* ─────────────────────────────────────
      FILTERS
     ─────────────────────────────────────*/
    public string $search           = '';
    public string $filterStatus     = '';
    public string $filterMonth      = '';
    public string $filterPayMethod  = '';

    protected $queryString = ['search', 'filterStatus', 'filterPayMethod'];

    /* ─────────────────────────────────────
      BOOT
     ─────────────────────────────────────*/
    public function boot(ContasReceberService $service): void
    {
        $service->syncOverdueStatus();
    }

    /* ─────────────────────────────────────
      COMPUTED — Accounts list (paginated)
     ─────────────────────────────────────*/
    #[Computed]
    public function accounts()
    {
        $query = AccountReceivable::with(['client', 'chartOfAccount'])
            ->orderBy('due_date_at');

        if ($this->search !== '') {
            $q = '%' . $this->search . '%';
            $query->where(function ($qry) use ($q) {
                $qry->where('description_title', 'like', $q)
                    ->orWhereHas('client', fn ($c) => $c->where('name', 'like', $q)
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

        if ($this->filterPayMethod !== '') {
            $query->where('payment_method', $this->filterPayMethod);
        }

        return $query->paginate(15);
    }

    /* ─────────────────────────────────────
      COMPUTED — KPIs
     ─────────────────────────────────────*/
    #[Computed]
    public function kpis(): array
    {
        return app(ContasReceberService::class)->getKpiData();
    }

    /* ─────────────────────────────────────
      COMPUTED — Selects
     ─────────────────────────────────────*/
    #[Computed]
    public function clients()
    {
        return Client::orderBy('name')->get(['id', 'name', 'social_name']);
    }

    #[Computed]
    public function chartAccounts()
    {
        return PlanOfAccount::where('is_active', true)
            ->where('is_selectable', true)
            ->where('type', 'receita')
            ->orderBy('code')
            ->get(['id', 'code', 'name']);
    }

    /* ─────────────────────────────────────
      MODAL OPEN — CREATE
     ─────────────────────────────────────*/
    public function openCreate(): void
    {
        $this->form->reset();
        $this->form->status      = ReceivableStatus::Pending->value;
        $this->form->due_date_at = today()->format('Y-m-d');
        $this->isEditing         = false;
        $this->editingId         = null;
        $this->showModal         = true;
    }

    /* ─────────────────────────────────────
      MODAL OPEN — EDIT
     ─────────────────────────────────────*/
    public function openEdit(int $id): void
    {
        $account = AccountReceivable::findOrFail($id);

        $this->form->fill([
            'description_title'   => $account->description_title ?? '',
            'client_id'           => $account->client_id,
            'chart_of_account_id' => $account->chart_of_account_id,
            'amount'              => (string) $account->amount,
            'due_date_at'         => $account->due_date_at?->format('Y-m-d') ?? '',
            'payment_method'      => $account->payment_method,
            'installment_number'  => $account->installment_number ?? 1,
            'status'              => $account->status->value ?? ReceivableStatus::Pending->value,
            'observation'         => $account->observation,
        ]);

        $this->editingId = $id;
        $this->isEditing = true;
        $this->showModal = true;
    }

    /* ─────────────────────────────────────
      SAVE
     ─────────────────────────────────────*/
    public function save(ContasReceberService $service): void
    {
        $this->form->validate();

        $data = [
            'description_title'   => $this->form->description_title,
            'client_id'           => $this->form->client_id ?: null,
            'chart_of_account_id' => $this->form->chart_of_account_id ?: null,
            'amount'              => (float) str_replace(['.', ','], ['', '.'], $this->form->amount),
            'due_date_at'         => $this->form->due_date_at,
            'payment_method'      => $this->form->payment_method ?: null,
            'installment_number'  => $this->form->installment_number ?: 1,
            'status'              => $this->form->status,
            'observation'         => $this->form->observation ?: null,
        ];

        if ($this->isEditing) {
            $service->update(AccountReceivable::findOrFail($this->editingId), $data);
            session()->flash('success', 'Conta a receber atualizada com sucesso!');
        } else {
            $service->create($data);
            session()->flash('success', 'Conta a receber cadastrada com sucesso!');
        }

        $this->closeModal();
    }

    /* ─────────────────────────────────────
      RECEIPT MODAL (Baixa)
     ─────────────────────────────────────*/
    public function openReceipt(int $id): void
    {
        $account = AccountReceivable::findOrFail($id);

        $this->receivingId        = $id;
        $this->receipt_date        = today()->format('Y-m-d');
        $this->receipt_amount      = (string) $account->amount;
        $this->receipt_observation = '';
        $this->showReceiptModal    = true;
    }

    public function registerReceipt(ContasReceberService $service): void
    {
        $this->validate([
            'receipt_date'   => 'required|date',
            'receipt_amount' => 'required|numeric|min:0.01',
        ], [
            'receipt_date.required'   => 'Informe a data do recebimento.',
            'receipt_amount.required' => 'Informe o valor recebido.',
            'receipt_amount.min'      => 'O valor recebido deve ser maior que zero.',
        ]);

        $service->registerReceipt(
            AccountReceivable::findOrFail($this->receivingId),
            $this->receipt_date,
            (float) str_replace(['.', ','], ['', '.'], $this->receipt_amount),
            $this->receipt_observation ?: null
        );

        session()->flash('success', 'Recebimento registrado com sucesso!');
        $this->closeReceiptModal();
    }

    public function closeReceiptModal(): void
    {
        $this->showReceiptModal    = false;
        $this->receivingId         = null;
        $this->receipt_date        = '';
        $this->receipt_amount      = '';
        $this->receipt_observation = '';
        $this->resetErrorBag(['receipt_date', 'receipt_amount', 'receipt_observation']);
    }

    /* ─────────────────────────────────────
      RESCHEDULE MODAL
     ─────────────────────────────────────*/
    public function openReschedule(int $id): void
    {
        $account = AccountReceivable::findOrFail($id);

        $this->reschedulingId     = $id;
        $this->reschedule_date    = $account->due_date_at?->format('Y-m-d') ?? '';
        $this->showRescheduleModal = true;
    }

    public function reschedule(ContasReceberService $service): void
    {
        $this->validate([
            'reschedule_date' => 'required|date',
        ], [
            'reschedule_date.required' => 'Informe a nova data de vencimento.',
        ]);

        $service->reschedule(
            AccountReceivable::findOrFail($this->reschedulingId),
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

    public function delete(ContasReceberService $service): void
    {
        if ($this->deletingId) {
            $service->delete(AccountReceivable::findOrFail($this->deletingId));
            session()->flash('success', 'Conta a receber excluída com sucesso!');
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
    public function cancelAccount(int $id, ContasReceberService $service): void
    {
        $service->cancel(AccountReceivable::findOrFail($id));
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
    public function updatingSearch(): void       { $this->resetPage(); }
    public function updatingFilterStatus(): void  { $this->resetPage(); }
    public function updatingFilterMonth(): void   { $this->resetPage(); }
    public function updatingFilterPayMethod(): void { $this->resetPage(); }

    /* ─────────────────────────────────────
      RENDER
     ─────────────────────────────────────*/
    public function render(): View
    {
        return view('livewire.financeiro.contas-receber.index', [
            'accounts'     => $this->accounts,
            'kpis'         => $this->kpis,
            'clients'      => $this->clients,
            'chartAccounts'=> $this->chartAccounts,
            'statuses'     => ReceivableStatus::cases(),
        ]);
    }
}

