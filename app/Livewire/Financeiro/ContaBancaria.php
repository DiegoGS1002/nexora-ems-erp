<?php

namespace App\Livewire\Financeiro;

use App\Models\BankAccount;
use App\Models\PlanOfAccount;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Contas Bancárias')]
class ContaBancaria extends Component
{
    /* ─────────────────────────────────────
    | MODAL STATE
    ─────────────────────────────────────*/
    public bool  $showModal        = false;
    public bool  $isEditing        = false;
    public ?int  $editingId        = null;
    public bool  $showTransferModal = false;
    public bool  $showDeleteModal  = false;
    public ?int  $deletingId       = null;

    /* ─────────────────────────────────────
    | FORM FIELDS
    ─────────────────────────────────────*/
    public string  $form_name               = '';
    public string  $form_bank_name          = '';
    public string  $form_agency             = '';
    public string  $form_number             = '';
    public string  $form_type               = 'corrente';
    public string  $form_balance            = '0';
    public string  $form_predicted_balance  = '0';
    public string  $form_color              = '';
    public ?int    $form_chart_of_account_id = null;
    public bool    $form_is_active          = true;
    public bool    $form_is_reconciled      = false;
    public string  $form_description        = '';

    /* ─────────────────────────────────────
    | TRANSFER FORM
    ─────────────────────────────────────*/
    public ?int   $transfer_from    = null;
    public ?int   $transfer_to      = null;
    public string $transfer_amount  = '0';
    public string $transfer_description = '';

    /* ─────────────────────────────────────
    | FILTERS / SEARCH
    ─────────────────────────────────────*/
    public string $search      = '';
    public string $filterType  = '';
    public string $filterStatus = '';

    /* ─────────────────────────────────────
    | VALIDATION — Account Form
    ─────────────────────────────────────*/
    protected function rules(): array
    {
        return [
            'form_name'      => 'required|string|max:255',
            'form_bank_name' => 'required|string|max:255',
            'form_type'      => 'required|in:corrente,poupanca,caixa_interno,digital',
            'form_balance'   => 'required|numeric|min:0',
        ];
    }

    protected function messages(): array
    {
        return [
            'form_name.required'      => 'O nome da conta é obrigatório.',
            'form_bank_name.required' => 'O nome do banco é obrigatório.',
            'form_type.required'      => 'O tipo de conta é obrigatório.',
            'form_balance.numeric'    => 'O saldo deve ser um valor numérico.',
        ];
    }

    protected function transferRules(): array
    {
        return [
            'transfer_from'   => 'required|different:transfer_to',
            'transfer_to'     => 'required|different:transfer_from',
            'transfer_amount' => 'required|numeric|min:0.01',
        ];
    }

    protected function transferMessages(): array
    {
        return [
            'transfer_from.required'     => 'Selecione a conta de origem.',
            'transfer_to.required'       => 'Selecione a conta de destino.',
            'transfer_from.different'    => 'A conta de origem e destino não podem ser iguais.',
            'transfer_amount.required'   => 'Informe o valor da transferência.',
            'transfer_amount.min'        => 'O valor deve ser maior que zero.',
        ];
    }

    /* ─────────────────────────────────────
    | MODAL OPEN — CREATE
    ─────────────────────────────────────*/
    public function openCreate(): void
    {
        $this->resetFormFields();
        $this->isEditing = false;
        $this->showModal = true;
    }

    /* ─────────────────────────────────────
    | MODAL OPEN — EDIT
    ─────────────────────────────────────*/
    public function openEdit(int $id): void
    {
        $account = BankAccount::findOrFail($id);

        $this->editingId                 = $id;
        $this->form_name                 = $account->name ?? '';
        $this->form_bank_name            = $account->bank_name ?? '';
        $this->form_agency               = $account->agency ?? '';
        $this->form_number               = $account->number ?? '';
        $this->form_type                 = $account->type ?? 'corrente';
        $this->form_balance              = (string) $account->balance;
        $this->form_predicted_balance    = (string) $account->predicted_balance;
        $this->form_color                = $account->color ?? '';
        $this->form_chart_of_account_id  = $account->chart_of_account_id;
        $this->form_is_active            = (bool) $account->is_active;
        $this->form_is_reconciled        = (bool) $account->is_reconciled;
        $this->form_description          = $account->description ?? '';

        $this->isEditing = true;
        $this->showModal = true;
    }

    /* ─────────────────────────────────────
    | SAVE
    ─────────────────────────────────────*/
    public function save(): void
    {
        $this->validate();

        $data = [
            'name'                  => $this->form_name,
            'bank_name'             => $this->form_bank_name,
            'agency'                => $this->form_agency ?: null,
            'number'                => $this->form_number ?: null,
            'type'                  => $this->form_type,
            'balance'               => (float) str_replace(['.', ','], ['', '.'], $this->form_balance),
            'predicted_balance'     => (float) str_replace(['.', ','], ['', '.'], $this->form_predicted_balance),
            'color'                 => $this->form_color ?: null,
            'chart_of_account_id'   => $this->form_chart_of_account_id ?: null,
            'is_active'             => $this->form_is_active,
            'is_reconciled'         => $this->form_is_reconciled,
            'description'           => $this->form_description ?: null,
        ];

        if ($this->isEditing) {
            BankAccount::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Conta bancária atualizada com sucesso!');
        } else {
            BankAccount::create($data);
            session()->flash('success', 'Conta bancária cadastrada com sucesso!');
        }

        $this->closeModal();
    }

    /* ─────────────────────────────────────
    | DELETE CONFIRMATION
    ─────────────────────────────────────*/
    public function confirmDelete(int $id): void
    {
        $this->deletingId       = $id;
        $this->showDeleteModal  = true;
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            BankAccount::findOrFail($this->deletingId)->delete();
            session()->flash('success', 'Conta bancária excluída com sucesso!');
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
    | TOGGLE ACTIVE
    ─────────────────────────────────────*/
    public function toggleActive(int $id): void
    {
        $account = BankAccount::findOrFail($id);
        $account->update(['is_active' => ! $account->is_active]);
    }

    /* ─────────────────────────────────────
    | TOGGLE RECONCILED
    ─────────────────────────────────────*/
    public function toggleReconciled(int $id): void
    {
        $account = BankAccount::findOrFail($id);
        $account->update([
            'is_reconciled'      => ! $account->is_reconciled,
            'last_reconciled_at' => ! $account->is_reconciled ? now() : null,
        ]);
    }

    /* ─────────────────────────────────────
    | TRANSFER BETWEEN ACCOUNTS
    ─────────────────────────────────────*/
    public function openTransfer(): void
    {
        $this->transfer_from        = null;
        $this->transfer_to          = null;
        $this->transfer_amount      = '0';
        $this->transfer_description = '';
        $this->showTransferModal    = true;
    }

    public function transfer(): void
    {
        $this->validateOnly('transfer_from', $this->transferRules(), $this->transferMessages());
        $this->validateOnly('transfer_to', $this->transferRules(), $this->transferMessages());
        $this->validateOnly('transfer_amount', $this->transferRules(), $this->transferMessages());

        $from   = BankAccount::findOrFail($this->transfer_from);
        $to     = BankAccount::findOrFail($this->transfer_to);
        $amount = (float) str_replace(['.', ','], ['', '.'], $this->transfer_amount);

        if ($from->balance < $amount) {
            $this->addError('transfer_amount', 'Saldo insuficiente na conta de origem.');
            return;
        }

        $from->decrement('balance', $amount);
        $to->increment('balance', $amount);

        $this->showTransferModal = false;
        session()->flash('success', "Transferência de R$ " . number_format($amount, 2, ',', '.') . " realizada com sucesso!");
    }

    public function closeTransfer(): void
    {
        $this->showTransferModal = false;
    }

    /* ─────────────────────────────────────
    | CLOSE MODAL
    ─────────────────────────────────────*/
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetFormFields();
    }

    /* ─────────────────────────────────────
    | RESET FORM
    ─────────────────────────────────────*/
    protected function resetFormFields(): void
    {
        $this->form_name                = '';
        $this->form_bank_name           = '';
        $this->form_agency              = '';
        $this->form_number              = '';
        $this->form_type                = 'corrente';
        $this->form_balance             = '0';
        $this->form_predicted_balance   = '0';
        $this->form_color               = '';
        $this->form_chart_of_account_id = null;
        $this->form_is_active           = true;
        $this->form_is_reconciled       = false;
        $this->form_description         = '';
        $this->editingId                = null;
        $this->resetErrorBag();
    }

    /* ─────────────────────────────────────
    | COMPUTED PROPERTIES
    ─────────────────────────────────────*/
    public function getAccountsProperty()
    {
        $query = BankAccount::with('chartOfAccount');

        if ($this->search !== '') {
            $q = '%' . $this->search . '%';
            $query->where(function ($qry) use ($q) {
                $qry->where('name', 'like', $q)
                    ->orWhere('bank_name', 'like', $q)
                    ->orWhere('number', 'like', $q);
            });
        }

        if ($this->filterType !== '') {
            $query->where('type', $this->filterType);
        }

        if ($this->filterStatus === 'active') {
            $query->where('is_active', true);
        } elseif ($this->filterStatus === 'inactive') {
            $query->where('is_active', false);
        }

        return $query->orderBy('name')->get();
    }

    public function getAllAccountsProperty()
    {
        return BankAccount::where('is_active', true)->orderBy('name')->get();
    }

    public function getChartAccountsProperty()
    {
        return PlanOfAccount::where('is_active', true)
            ->where('is_selectable', true)
            ->where('type', 'ativo')
            ->orderBy('code')
            ->get();
    }

    public function getTotalBalanceProperty(): float
    {
        return (float) BankAccount::where('is_active', true)->sum('balance');
    }

    public function getTotalPredictedProperty(): float
    {
        return (float) BankAccount::where('is_active', true)->sum('predicted_balance');
    }

    public function getActiveCountProperty(): int
    {
        return BankAccount::where('is_active', true)->count();
    }

    public function getTotalCountProperty(): int
    {
        return BankAccount::count();
    }

    public function getReconciledCountProperty(): int
    {
        return BankAccount::where('is_reconciled', true)->where('is_active', true)->count();
    }

    /* ─────────────────────────────────────
    | RENDER
    ─────────────────────────────────────*/
    public function render()
    {
        return view('livewire.financeiro.conta-bancaria.index', [
            'accounts'       => $this->getAccountsProperty(),
            'allAccounts'    => $this->getAllAccountsProperty(),
            'chartAccounts'  => $this->getChartAccountsProperty(),
            'totalBalance'   => $this->getTotalBalanceProperty(),
            'totalPredicted' => $this->getTotalPredictedProperty(),
            'activeCount'    => $this->getActiveCountProperty(),
            'totalCount'     => $this->getTotalCountProperty(),
            'reconciledCount'=> $this->getReconciledCountProperty(),
        ]);
    }
}

