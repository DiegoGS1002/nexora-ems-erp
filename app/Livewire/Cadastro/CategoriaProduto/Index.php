<?php

namespace App\Livewire\Cadastro\CategoriaProduto;

use App\Models\ProductCategory;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Categorias de Produto')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'search')]
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function deleteCategory(string $id): void
    {
        $category = ProductCategory::find($id);

        if (! $category) {
            return;
        }

        if ($category->products()->count() > 0) {
            session()->flash('error', 'Não é possível excluir esta categoria pois há produtos associados a ela.');
            return;
        }

        $category->delete();

        session()->flash('success', 'Categoria excluída com sucesso!');
        $this->resetPage();
    }

    public function render()
    {
        $categories = ProductCategory::query()
            ->withCount('products')
            ->when($this->search !== '', function ($query) {
                $query->where('name', 'like', "%{$this->search}%");
            })
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.cadastro.categoria-produto.index', [
            'categories' => $categories,
        ]);
    }
}

