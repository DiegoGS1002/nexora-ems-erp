<?php

namespace App\Livewire\Cadastro\UnidadeMedida;

use App\Models\UnitOfMeasure;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Unidades de Medida')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'search')]
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function deleteUnit(string $id): void
    {
        $unit = UnitOfMeasure::find($id);

        if (! $unit) {
            return;
        }

        if ($unit->products()->count() > 0) {
            session()->flash('error', 'Não é possível excluir esta unidade pois há produtos associados a ela.');
            return;
        }

        $unit->delete();

        session()->flash('success', 'Unidade de medida excluída com sucesso!');
        $this->resetPage();
    }

    public function render()
    {
        $units = UnitOfMeasure::query()
            ->withCount('products')
            ->when($this->search !== '', function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('abbreviation', 'like', "%{$this->search}%");
            })
            ->orderBy('abbreviation')
            ->paginate(15);

        return view('livewire.cadastro.unidade-medida.index', [
            'units' => $units,
        ]);
    }
}

