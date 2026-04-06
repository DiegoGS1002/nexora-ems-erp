<?php

namespace App\Livewire\Cadastro\Veiculos;

use App\Enums\CategoriaVeiculo;
use App\Enums\CombustivelVeiculo;
use App\Enums\EspecieVeiculo;
use App\Enums\TipoVeiculo;
use App\Livewire\Forms\VehicleForm;
use App\Models\Vehicle;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class Form extends Component
{
    use WithFileUploads;

    public ?Vehicle $vehicle = null;

    public VehicleForm $form;

    public string $activeTab = 'dados-gerais';

    /** @var array<int, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile> */
    public array $newPhotos = [];

    /** @var array<int, string> */
    public array $existingPhotos = [];

    public function mount(?Vehicle $vehicle = null): void
    {
        $this->vehicle = $vehicle && $vehicle->exists ? $vehicle : null;

        if ($this->vehicle) {
            $this->existingPhotos = $this->vehicle->photos ?? [];
            $this->form->fill([
                'plate'              => $this->vehicle->plate,
                'renavam'            => $this->vehicle->renavam,
                'chassis'            => $this->vehicle->chassis,
                'vehicle_type'       => $this->vehicle->vehicle_type?->value ?? '',
                'category'           => $this->vehicle->category?->value ?? '',
                'species'            => $this->vehicle->species?->value ?? '',
                'manufacturing_year' => $this->vehicle->manufacturing_year ?? $this->vehicle->year ?? '',
                'model_year'         => $this->vehicle->model_year ?? $this->vehicle->year ?? '',
                'brand'              => $this->vehicle->brand,
                'model'              => $this->vehicle->model,
                'color'              => $this->vehicle->color,
                'fuel_type'          => $this->vehicle->fuel_type?->value ?? $this->vehicle->fuel_type ?? '',
                'power_hp'           => $this->vehicle->power_hp,
                'displacement_cc'    => $this->vehicle->displacement_cc,
                'doors'              => $this->vehicle->doors ? (string) $this->vehicle->doors : null,
                'passenger_capacity' => $this->vehicle->passenger_capacity ? (string) $this->vehicle->passenger_capacity : null,
                'transmission_type'  => $this->vehicle->transmission_type,
                'traction_type'      => $this->vehicle->traction_type,
                'gross_weight'       => $this->vehicle->gross_weight ? (string) $this->vehicle->gross_weight : null,
                'net_weight'         => $this->vehicle->net_weight ? (string) $this->vehicle->net_weight : null,
                'cargo_capacity'     => $this->vehicle->cargo_capacity ? (string) $this->vehicle->cargo_capacity : null,
                'department'         => $this->vehicle->department,
                'responsible_driver' => $this->vehicle->responsible_driver,
                'cost_center'        => $this->vehicle->cost_center,
                'unit'               => $this->vehicle->unit,
                'current_location'   => $this->vehicle->current_location,
                'location_note'      => $this->vehicle->location_note,
                'is_active'          => (bool) $this->vehicle->is_active,
                'acquisition_date'   => $this->vehicle->acquisition_date?->format('Y-m-d'),
                'acquisition_value'  => $this->vehicle->acquisition_value ? (string) $this->vehicle->acquisition_value : null,
                'observations'       => $this->vehicle->observations,
                'name'               => $this->vehicle->name,
            ]);
        }
    }

    public function removeExistingPhoto(int $index): void
    {
        array_splice($this->existingPhotos, $index, 1);
        $this->existingPhotos = array_values($this->existingPhotos);
    }

    public function removeNewPhoto(int $index): void
    {
        array_splice($this->newPhotos, $index, 1);
        $this->newPhotos = array_values($this->newPhotos);
    }

    public function save(): mixed
    {
        $vehicleId = $this->vehicle?->id;

        $this->form->validate();

        $this->validate([
            'form.plate' => [
                'required', 'string',
                Rule::unique('vehicles', 'plate')->ignore($vehicleId),
            ],
            'form.renavam' => [
                'required', 'string',
                Rule::unique('vehicles', 'renavam')->ignore($vehicleId),
            ],
            'form.chassis' => [
                'required', 'string',
                Rule::unique('vehicles', 'chassis')->ignore($vehicleId),
            ],
        ]);

        $uploadedPaths = [];
        foreach ($this->newPhotos as $photo) {
            $uploadedPaths[] = $photo->store('vehicles', 'public');
        }

        $allPhotos = array_merge($this->existingPhotos, $uploadedPaths);

        $payload = [
            'plate'              => strtoupper(trim($this->form->plate)),
            'renavam'            => trim($this->form->renavam),
            'chassis'            => strtoupper(trim($this->form->chassis)),
            'vehicle_type'       => $this->form->vehicle_type ?: null,
            'category'           => $this->form->category ?: null,
            'species'            => $this->form->species ?: null,
            'manufacturing_year' => $this->form->manufacturing_year,
            'model_year'         => $this->form->model_year,
            'year'               => $this->form->manufacturing_year,
            'brand'              => $this->form->brand,
            'model'              => $this->form->model,
            'color'              => $this->form->color,
            'fuel_type'          => $this->form->fuel_type ?: null,
            'power_hp'           => $this->form->power_hp ?: null,
            'displacement_cc'    => $this->form->displacement_cc ?: null,
            'doors'              => $this->form->doors ?: null,
            'passenger_capacity' => $this->form->passenger_capacity ?: null,
            'transmission_type'  => $this->form->transmission_type ?: null,
            'traction_type'      => $this->form->traction_type ?: null,
            'gross_weight'       => $this->form->gross_weight ?: null,
            'net_weight'         => $this->form->net_weight ?: null,
            'cargo_capacity'     => $this->form->cargo_capacity ?: null,
            'department'         => $this->form->department ?: null,
            'responsible_driver' => $this->form->responsible_driver ?: null,
            'cost_center'        => $this->form->cost_center ?: null,
            'unit'               => $this->form->unit ?: null,
            'current_location'   => $this->form->current_location ?: null,
            'location_note'      => $this->form->location_note ?: null,
            'is_active'          => $this->form->is_active,
            'acquisition_date'   => $this->form->acquisition_date ?: null,
            'acquisition_value'  => $this->form->acquisition_value ?: null,
            'photos'             => ! empty($allPhotos) ? $allPhotos : null,
            'observations'       => $this->form->observations ?: null,
            'name'               => $this->form->name ?: null,
        ];

        if ($this->vehicle) {
            $this->vehicle->update($payload);

            return redirect()->route('vehicles.index')
                ->with('success', 'Veículo atualizado com sucesso!');
        }

        Vehicle::create($payload);

        return redirect()->route('vehicles.index')
            ->with('success', 'Veículo cadastrado com sucesso!');
    }

    public function render()
    {
        $title = $this->vehicle ? 'Editar Veículo' : 'Novo Veículo';

        return view('livewire.cadastro.veiculos.form', [
            'isEditing'    => (bool) $this->vehicle,
            'tiposVeiculo' => TipoVeiculo::cases(),
            'categorias'   => CategoriaVeiculo::cases(),
            'especies'     => EspecieVeiculo::cases(),
            'combustiveis' => CombustivelVeiculo::cases(),
        ])->title($title);
    }
}

