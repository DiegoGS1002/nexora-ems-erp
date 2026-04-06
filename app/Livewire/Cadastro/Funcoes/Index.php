<?php

namespace App\Livewire\Cadastro\Funcoes;

use App\Models\Role;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Funções / Cargos')]
class Index extends Component
{
    public string $search = '';

    public function deleteRole(int $roleId): void
    {
        Role::where('id', $roleId)->delete();

        session()->flash('success', 'Função excluída com sucesso!');
    }

    #[Computed]
    public function roles()
    {
        return Role::query()
            ->with('parentRole')
            ->when($this->search !== '', function ($query) {
                $query->where(function ($sub) {
                    $sub->where('name', 'like', "%{$this->search}%")
                        ->orWhere('department', 'like', "%{$this->search}%")
                        ->orWhere('code', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.cadastro.funcoes.index');
    }
}

