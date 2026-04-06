<?php

namespace App\Livewire\Cadastro\Fornecedores;

use App\Models\Supplier;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Fornecedores')]
class Index extends Component
{
    public string $search = '';

    public function deleteSupplier(string $supplierId): void
    {
        Supplier::where('id', $supplierId)->delete();

        session()->flash('success', 'Fornecedor removido com sucesso!');
    }

    #[Computed]
    public function suppliers()
    {
        return Supplier::query()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($sub) {
                    $sub->where('social_name', 'like', "%{$this->search}%")
                        ->orWhere('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('taxNumber', 'like', "%{$this->search}%")
                        ->orWhere('address_city', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('social_name')
            ->get();
    }

    public function render()
    {
        return view('livewire.cadastro.fornecedores.index');
    }
}
