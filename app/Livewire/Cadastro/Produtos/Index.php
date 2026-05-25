<?php

namespace App\Livewire\Cadastro\Produtos;

use App\Models\Product;
use App\Models\Supplier;
use App\Support\PerPage;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Produtos')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'search')]
    public string $search = '';

    #[Url(as: 'supplier_id')]
    public string $supplierId = '';

    #[Url(as: 'unit_of_measure')]
    public string $unitOfMeasure = '';

    #[Url(as: 'category')]
    public string $category = '';

    #[Url(as: 'expiration_date')]
    public string $expirationDate = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSupplierId(): void
    {
        $this->resetPage();
    }

    public function updatedUnitOfMeasure(): void
    {
        $this->resetPage();
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    public function updatedExpirationDate(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'supplierId', 'unitOfMeasure', 'category', 'expirationDate');
        $this->resetPage();
    }

    public function deleteProduct(string $productId): void
    {
        $product = Product::query()->find($productId);

        if (! $product) {
            return;
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        session()->flash('success', 'Produto deletado com sucesso!');

        $this->resetPage();
    }

    public function render()
    {
        $products = Product::query()
            ->with('suppliers')
            ->when($this->search !== '', function ($query) {
                $query->where('name', 'like', "%{$this->search}%");
            })
            ->when($this->supplierId !== '', function ($query) {
                $supplierId = $this->supplierId;

                $query->whereHas('suppliers', function ($subQuery) use ($supplierId) {
                    $subQuery->where('suppliers.id', $supplierId);
                });
            })
            ->when($this->unitOfMeasure !== '', function ($query) {
                $query->where('unit_of_measure', $this->unitOfMeasure);
            })
            ->when($this->category !== '', function ($query) {
                $query->where('category', $this->category);
            })
            ->when($this->expirationDate === 'expired', function ($query) {
                $query->whereDate('expiration_date', '<', now());
            })
            ->when($this->expirationDate === 'valid', function ($query) {
                $query->whereDate('expiration_date', '>=', now());
            })
            ->when($this->expirationDate === 'na', function ($query) {
                $query->whereNull('expiration_date');
            })
            ->orderBy('name')
            ->paginate(PerPage::resolve());

        return view('livewire.cadastro.produtos.index', [
            'products' => $products,
            'suppliers' => Supplier::query()->orderBy('social_name')->get(),
        ]);
    }
}


