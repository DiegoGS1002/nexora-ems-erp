<?php

namespace App\Livewire\Financeiro;

use App\Models\PlanOfAccount;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Plano de Contas')]
class PlanoContas extends Component
{
    /* ─────────────────────────────────────
     | MODAL STATE
     ─────────────────────────────────────*/
    public bool  $showModal  = false;
    public bool  $isEditing  = false;
    public ?int  $editingId  = null;

    /* ─────────────────────────────────────
     | FORM FIELDS
     ─────────────────────────────────────*/
    public string  $form_name         = '';
    public string  $form_code         = '';
    public string  $form_type         = 'receita';
    public ?int    $form_parent_id    = null;
    public bool    $form_is_selectable = true;
    public bool    $form_is_active     = true;
    public string  $form_description  = '';

    /* ─────────────────────────────────────
     | FILTERS / SEARCH
     ─────────────────────────────────────*/
    public string $search     = '';
    public string $filterType = '';

    /* ─────────────────────────────────────
     | VALIDATION
     ─────────────────────────────────────*/
    protected function rules(): array
    {
        return [
            'form_name' => 'required|string|max:255',
            'form_code' => 'required|string|max:30',
            'form_type' => 'required|in:receita,despesa,ativo,passivo',
        ];
    }

    protected function messages(): array
    {
        return [
            'form_name.required' => 'O nome da conta é obrigatório.',
            'form_code.required' => 'O código da conta é obrigatório.',
            'form_type.required' => 'O tipo é obrigatório.',
        ];
    }

    /* ─────────────────────────────────────
     | MODAL OPEN — CREATE
     ─────────────────────────────────────*/
    public function openCreate(?int $parentId = null): void
    {
        $this->resetFormFields();

        if ($parentId !== null) {
            $this->form_parent_id = $parentId;
            $parent = PlanOfAccount::find($parentId);
            if ($parent) {
                $this->form_type = $parent->type ?? 'receita';
            }
        }

        $this->isEditing = false;
        $this->showModal = true;
    }

    /* ─────────────────────────────────────
     | MODAL OPEN — EDIT
     ─────────────────────────────────────*/
    public function openEdit(int $id): void
    {
        $account = PlanOfAccount::findOrFail($id);

        $this->editingId            = $id;
        $this->form_name            = $account->name ?? '';
        $this->form_code            = $account->code ?? '';
        $this->form_type            = $account->type ?? 'receita';
        $this->form_parent_id       = $account->parent_id;
        $this->form_is_selectable   = (bool) $account->is_selectable;
        $this->form_is_active       = (bool) $account->is_active;
        $this->form_description     = $account->description ?? '';

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
            'name'          => $this->form_name,
            'code'          => $this->form_code,
            'type'          => $this->form_type,
            'parent_id'     => $this->form_parent_id ?: null,
            'is_selectable' => $this->form_is_selectable,
            'is_active'     => $this->form_is_active,
            'description'   => $this->form_description ?: null,
        ];

        if ($this->isEditing) {
            PlanOfAccount::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Conta atualizada com sucesso!');
        } else {
            PlanOfAccount::create($data);
            session()->flash('success', 'Conta criada com sucesso!');
        }

        $this->closeModal();
    }

    /* ─────────────────────────────────────
     | DELETE
     ─────────────────────────────────────*/
    public function confirmDelete(int $id): void
    {
        $account = PlanOfAccount::findOrFail($id);

        if ($account->children()->exists()) {
            session()->flash('error', 'Não é possível excluir uma conta que possui subcontas. Remova as subcontas primeiro.');
            return;
        }

        $account->delete();
        session()->flash('success', 'Conta excluída com sucesso!');
    }

    /* ─────────────────────────────────────
     | TOGGLE ACTIVE
     ─────────────────────────────────────*/
    public function toggleActive(int $id): void
    {
        $account = PlanOfAccount::findOrFail($id);
        $account->update(['is_active' => ! $account->is_active]);
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
        $this->form_name          = '';
        $this->form_code          = '';
        $this->form_type          = 'receita';
        $this->form_parent_id     = null;
        $this->form_is_selectable = true;
        $this->form_is_active     = true;
        $this->form_description   = '';
        $this->editingId          = null;
        $this->resetErrorBag();
    }

    /* ─────────────────────────────────────
     | DATA HELPERS
     ─────────────────────────────────────*/

    /**
     * Build a flat list (with depth) for search results.
     */
    protected function buildFlatList(iterable $accounts, int $depth = 0): array
    {
        $result = [];
        foreach ($accounts as $account) {
            $result[] = [
                'model' => $account,
                'depth' => $depth,
            ];
            if ($account->relationLoaded('children') && $account->children->isNotEmpty()) {
                $result = array_merge($result, $this->buildFlatList($account->children, $depth + 1));
            }
        }
        return $result;
    }

    /* ─────────────────────────────────────
     | COMPUTED PROPERTIES
     ─────────────────────────────────────*/

    public function getTreeProperty()
    {
        $nested = [
            'children',
            'children.children',
            'children.children.children',
            'children.children.children.children',
            'children.children.children.children.children',
        ];

        $query = PlanOfAccount::with($nested)->whereNull('parent_id');

        if ($this->filterType !== '') {
            $query->where('type', $this->filterType);
        }

        return $query->orderBy('code')->get();
    }

    public function getSearchResultsProperty()
    {
        if (trim($this->search) === '') {
            return collect();
        }

        $q = '%' . $this->search . '%';

        return PlanOfAccount::where('name', 'like', $q)
            ->orWhere('code', 'like', $q)
            ->when($this->filterType !== '', fn ($qry) => $qry->where('type', $this->filterType))
            ->orderBy('code')
            ->get();
    }

    public function getAllAccountsProperty()
    {
        return PlanOfAccount::orderBy('code')->get();
    }

    public function getTotalCountProperty(): int
    {
        return PlanOfAccount::count();
    }

    public function getActiveCountProperty(): int
    {
        return PlanOfAccount::where('is_active', true)->count();
    }

    public function getGroupCountProperty(): int
    {
        return PlanOfAccount::has('children')->count();
    }

    /* ─────────────────────────────────────
     | RENDER
     ─────────────────────────────────────*/
    public function render()
    {
        return view('livewire.financeiro.plano-contas.index', [
            // Computed properties are also accessible as $this->xxx in the view,
            // but we pass them explicitly for IDE support and cleaner templates.
            'tree'          => $this->getTreeProperty(),
            'searchResults' => $this->getSearchResultsProperty(),
            'allAccounts'   => $this->getAllAccountsProperty(),
            'totalCount'    => $this->getTotalCountProperty(),
            'activeCount'   => $this->getActiveCountProperty(),
            'groupCount'    => $this->getGroupCountProperty(),
        ]);
    }
}


