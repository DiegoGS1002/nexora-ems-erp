<?php

namespace App\Livewire\Fiscal\GrupoTributario;

use App\Enums\RegimeTributario;
use App\Models\GrupoTributario;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Grupos Tributários')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'search')]
    public string $search = '';

    #[Url(as: 'regime')]
    public string $filterRegime = '';

    public function updatedSearch(): void    { $this->resetPage(); }
    public function updatedFilterRegime(): void { $this->resetPage(); }

    public function delete(int $id): void
    {
        $grupo = GrupoTributario::find($id);
        if (!$grupo) return;

        if ($grupo->products()->count() > 0) {
            session()->flash('error', 'Não é possível excluir este grupo pois há produtos vinculados.');
            return;
        }

        $grupo->delete();
        session()->flash('success', 'Grupo tributário excluído com sucesso!');
        $this->resetPage();
    }

    public function render()
    {
        $grupos = GrupoTributario::query()
            ->withCount('products')
            ->with(['tipoOperacaoSaida', 'tipoOperacaoEntrada'])
            ->when($this->search !== '', function ($q) {
                $q->where(function ($q2) {
                    $q2->where('codigo', 'like', "%{$this->search}%")
                       ->orWhere('nome', 'like', "%{$this->search}%")
                       ->orWhere('ncm', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterRegime !== '', fn($q) => $q->where('regime_tributario', $this->filterRegime))
            ->orderBy('codigo')
            ->paginate(15);

        return view('livewire.fiscal.grupo-tributario.index', [
            'grupos'  => $grupos,
            'regimes' => RegimeTributario::cases(),
        ]);
    }
}

