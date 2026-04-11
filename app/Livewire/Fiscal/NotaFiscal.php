<?php

namespace App\Livewire\Fiscal;

use App\Enums\FiscalNoteStatus;
use App\Models\Client;
use App\Models\FiscalNote;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Notas Fiscais')]
class NotaFiscal extends Component
{
    use WithPagination;

    /* ─────────────────────────────────────
      FILTERS
     ─────────────────────────────────────*/
    public string $search          = '';
    public string $filterStatus    = '';
    public string $filterType      = '';
    public string $filterEnv       = '';

    protected $queryString = ['search', 'filterStatus', 'filterType', 'filterEnv'];

    /* ─────────────────────────────────────
      MODAL STATE
     ─────────────────────────────────────*/
    public bool  $showModal        = false;
    public bool  $isEditing        = false;
    public ?int  $editingId        = null;

    public bool  $showViewModal    = false;
    public ?int  $viewingId        = null;

    public bool  $showCancelModal  = false;
    public ?int  $cancellingId     = null;

    public bool  $showDeleteModal  = false;
    public ?int  $deletingId       = null;

    /* ─────────────────────────────────────
      FORM FIELDS
     ─────────────────────────────────────*/
    public string  $form_client_id     = '';
    public string  $form_client_name   = '';
    public string  $form_invoice_number = '';
    public string  $form_series        = '1';
    public string  $form_type          = 'nfe';
    public string  $form_environment   = 'homologation';
    public string  $form_status        = 'draft';
    public string  $form_access_key    = '';
    public string  $form_protocol      = '';
    public string  $form_sefaz_message = '';
    public string  $form_amount        = '';
    public string  $form_notes         = '';

    /* ─────────────────────────────────────
      CANCEL FORM
     ─────────────────────────────────────*/
    public string $cancel_reason = '';

    /* ─────────────────────────────────────
      COMPUTED — Notes list (paginated)
     ─────────────────────────────────────*/
    #[Computed]
    public function notes()
    {
        $query = FiscalNote::with('client')
            ->orderByDesc('created_at');

        if ($this->search !== '') {
            $q = '%' . $this->search . '%';
            $query->where(function ($qry) use ($q) {
                $qry->where('invoice_number', 'like', $q)
                    ->orWhere('client_name', 'like', $q)
                    ->orWhere('access_key', 'like', $q)
                    ->orWhereHas('client', fn($s) =>
                        $s->where('name', 'like', $q)->orWhere('social_name', 'like', $q)
                    );
            });
        }

        if ($this->filterStatus !== '') {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterType !== '') {
            $query->where('type', $this->filterType);
        }

        if ($this->filterEnv !== '') {
            $query->where('environment', $this->filterEnv);
        }

        return $query->paginate(15);
    }

    /* ─────────────────────────────────────
      COMPUTED — KPIs
     ─────────────────────────────────────*/
    #[Computed]
    public function kpis(): array
    {
        $total      = FiscalNote::count();
        $authorized = FiscalNote::where('status', FiscalNoteStatus::Authorized)->count();
        $rejected   = FiscalNote::where('status', FiscalNoteStatus::Rejected)->count();
        $cancelled  = FiscalNote::where('status', FiscalNoteStatus::Cancelled)->count();
        $totalValue = FiscalNote::where('status', FiscalNoteStatus::Authorized)->sum('amount');

        return compact('total', 'authorized', 'rejected', 'cancelled', 'totalValue');
    }

    /* ─────────────────────────────────────
      COMPUTED — Clients list
     ─────────────────────────────────────*/
    #[Computed]
    public function clients()
    {
        return Client::orderBy('social_name')->get(['id', 'name', 'social_name', 'taxNumber']);
    }

    /* ─────────────────────────────────────
      COMPUTED — Note being viewed
     ─────────────────────────────────────*/
    #[Computed]
    public function viewingNote(): ?FiscalNote
    {
        return $this->viewingId ? FiscalNote::with('client', 'emittedBy')->find($this->viewingId) : null;
    }

    /* ─────────────────────────────────────
      MODAL OPEN — CREATE
     ─────────────────────────────────────*/
    public function openCreate(): void
    {
        $this->resetForm();
        $this->form_invoice_number = $this->nextInvoiceNumber();
        $this->isEditing = false;
        $this->editingId = null;
        $this->showModal = true;
    }

    /* ─────────────────────────────────────
      MODAL OPEN — EDIT
     ─────────────────────────────────────*/
    public function openEdit(int $id): void
    {
        $note = FiscalNote::findOrFail($id);

        $this->form_client_id      = (string) ($note->client_id ?? '');
        $this->form_client_name    = $note->client_name ?? '';
        $this->form_invoice_number = $note->invoice_number;
        $this->form_series         = $note->series;
        $this->form_type           = $note->type;
        $this->form_environment    = $note->environment;
        $this->form_status         = $note->status->value;
        $this->form_access_key     = $note->access_key ?? '';
        $this->form_protocol       = $note->protocol ?? '';
        $this->form_sefaz_message  = $note->sefaz_message ?? '';
        $this->form_amount         = (string) $note->amount;
        $this->form_notes          = $note->notes ?? '';

        $this->editingId = $id;
        $this->isEditing = true;
        $this->showModal = true;
    }

    /* ─────────────────────────────────────
      SAVE (Create / Update)
     ─────────────────────────────────────*/
    public function save(): void
    {
        $this->validate([
            'form_invoice_number' => 'required|digits_between:1,9',
            'form_series'         => 'required|max:3',
            'form_type'           => 'required|in:nfe,nfce',
            'form_environment'    => 'required|in:production,homologation',
            'form_status'         => 'required',
            'form_amount'         => 'required|numeric|min:0',
        ], [
            'form_invoice_number.required' => 'O número da nota é obrigatório.',
            'form_invoice_number.digits_between' => 'O número deve ter até 9 dígitos.',
            'form_amount.required' => 'Informe o valor total.',
            'form_amount.min'      => 'O valor não pode ser negativo.',
        ]);

        // Resolve client name from id
        $clientName = $this->form_client_name;
        if ($this->form_client_id) {
            $client = Client::find($this->form_client_id);
            $clientName = $client ? ($client->social_name ?? $client->name) : $clientName;
        }

        $data = [
            'client_id'      => $this->form_client_id ?: null,
            'client_name'    => $clientName ?: null,
            'invoice_number' => $this->form_invoice_number,
            'series'         => $this->form_series,
            'type'           => $this->form_type,
            'environment'    => $this->form_environment,
            'status'         => $this->form_status,
            'access_key'     => $this->form_access_key ?: null,
            'protocol'       => $this->form_protocol ?: null,
            'sefaz_message'  => $this->form_sefaz_message ?: null,
            'amount'         => (float) str_replace(['.', ','], ['', '.'], $this->form_amount),
            'notes'          => $this->form_notes ?: null,
            'emitted_by'     => Auth::id(),
        ];

        // Set authorized_at when authorizing
        if ($this->form_status === 'authorized' && $this->isEditing) {
            $existing = FiscalNote::find($this->editingId);
            if ($existing && $existing->status !== FiscalNoteStatus::Authorized) {
                $data['authorized_at'] = now();
            }
        }

        if ($this->isEditing) {
            FiscalNote::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Nota fiscal atualizada com sucesso!');
        } else {
            $data['authorized_at'] = $this->form_status === 'authorized' ? now() : null;
            FiscalNote::create($data);
            session()->flash('success', 'Nota fiscal cadastrada com sucesso!');
        }

        $this->closeModal();
    }

    /* ─────────────────────────────────────
      VIEW MODAL
     ─────────────────────────────────────*/
    public function openView(int $id): void
    {
        $this->viewingId    = $id;
        $this->showViewModal = true;
    }

    public function closeView(): void
    {
        $this->showViewModal = false;
        $this->viewingId     = null;
    }

    /* ─────────────────────────────────────
      CANCEL MODAL
     ─────────────────────────────────────*/
    public function openCancel(int $id): void
    {
        $this->cancellingId   = $id;
        $this->cancel_reason  = '';
        $this->showCancelModal = true;
    }

    public function confirmCancel(): void
    {
        $this->validate([
            'cancel_reason' => 'required|min:15|max:255',
        ], [
            'cancel_reason.required' => 'Informe o motivo do cancelamento.',
            'cancel_reason.min'      => 'O motivo deve ter no mínimo 15 caracteres.',
        ]);

        $note = FiscalNote::findOrFail($this->cancellingId);
        $note->update([
            'status'       => FiscalNoteStatus::Cancelled->value,
            'cancel_reason'=> $this->cancel_reason,
            'cancelled_at' => now(),
        ]);

        session()->flash('success', 'Nota fiscal cancelada com sucesso.');
        $this->closeCancelModal();
    }

    public function closeCancelModal(): void
    {
        $this->showCancelModal = false;
        $this->cancellingId    = null;
        $this->cancel_reason   = '';
        $this->resetErrorBag(['cancel_reason']);
    }

    /* ─────────────────────────────────────
      DELETE
     ─────────────────────────────────────*/
    public function confirmDelete(int $id): void
    {
        $this->deletingId     = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            FiscalNote::findOrFail($this->deletingId)->delete();
            session()->flash('success', 'Nota fiscal excluída com sucesso.');
        }
        $this->cancelDelete();
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingId      = null;
    }

    /* ─────────────────────────────────────
      CLOSE MODAL / RESET FORM
     ─────────────────────────────────────*/
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->isEditing = false;
        $this->editingId = null;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->form_client_id      = '';
        $this->form_client_name    = '';
        $this->form_invoice_number = '';
        $this->form_series         = '1';
        $this->form_type           = 'nfe';
        $this->form_environment    = 'homologation';
        $this->form_status         = 'draft';
        $this->form_access_key     = '';
        $this->form_protocol       = '';
        $this->form_sefaz_message  = '';
        $this->form_amount         = '';
        $this->form_notes          = '';
        $this->resetErrorBag();
    }

    /* ─────────────────────────────────────
      PAGINATION RESET
     ─────────────────────────────────────*/
    public function updatingSearch(): void       { $this->resetPage(); }
    public function updatingFilterStatus(): void  { $this->resetPage(); }
    public function updatingFilterType(): void    { $this->resetPage(); }
    public function updatingFilterEnv(): void     { $this->resetPage(); }

    /* ─────────────────────────────────────
      HELPER
     ─────────────────────────────────────*/
    private function nextInvoiceNumber(): string
    {
        $last = FiscalNote::max('invoice_number') ?? '0';
        return str_pad((int) $last + 1, 6, '0', STR_PAD_LEFT);
    }

    /* ─────────────────────────────────────
      RENDER
     ─────────────────────────────────────*/
    public function render(): View
    {
        return view('livewire.fiscal.notas-fiscais.index', [
            'notes'    => $this->notes,
            'kpis'     => $this->kpis,
            'clients'  => $this->clients,
            'statuses' => FiscalNoteStatus::cases(),
        ]);
    }
}

