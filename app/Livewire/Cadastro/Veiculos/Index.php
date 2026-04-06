<?php

namespace App\Livewire\Cadastro\Veiculos;

use App\Models\Vehicle;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Veículos')]
class Index extends Component
{
    public string $search = '';

    public function deleteVehicle(int $vehicleId): void
    {
        Vehicle::where('id', $vehicleId)->delete();

        session()->flash('success', 'Veículo excluído com sucesso!');
    }

    #[Computed]
    public function vehicles()
    {
        return Vehicle::query()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($sub) {
                    $sub->where('plate', 'like', "%{$this->search}%")
                        ->orWhere('brand', 'like', "%{$this->search}%")
                        ->orWhere('model', 'like', "%{$this->search}%")
                        ->orWhere('renavam', 'like', "%{$this->search}%")
                        ->orWhere('chassis', 'like', "%{$this->search}%")
                        ->orWhere('responsible_driver', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('brand')
            ->orderBy('model')
            ->get();
    }

    public function render()
    {
        return view('livewire.cadastro.veiculos.index');
    }
}

