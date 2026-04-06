<?php

namespace App\Livewire\Cadastro\Clientes;

use App\Models\Client;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Clientes')]
class Index extends Component
{
    public string $search = '';

    public function deleteClient(string $clientId): void
    {
        Client::where('id', $clientId)->delete();

        session()->flash('success', 'Cliente removido com sucesso!');
    }

    #[Computed]
    public function clients()
    {
        return Client::query()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($sub) {
                    $sub->where('name', 'like', "%{$this->search}%")
                        ->orWhere('social_name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('taxNumber', 'like', "%{$this->search}%")
                        ->orWhere('address_city', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.cadastro.clientes.index');
    }
}
