<?php

namespace App\Livewire\Cadastro\UnidadeMedida;

use App\Livewire\Forms\UnitOfMeasureForm;
use App\Models\UnitOfMeasure;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Form extends Component
{
    public ?UnitOfMeasure $unit = null;

    public UnitOfMeasureForm $form;

    public function mount(?UnitOfMeasure $unit = null): void
    {
        $this->unit = $unit && $unit->exists ? $unit : null;

        if ($this->unit) {
            $this->form->name         = $this->unit->name;
            $this->form->abbreviation = $this->unit->abbreviation;
            $this->form->description  = $this->unit->description;
            $this->form->is_active    = (bool) $this->unit->is_active;
        }
    }

    public function save(): mixed
    {
        $this->form->validate();

        $unitId = $this->unit?->id;

        $this->validate([
            'form.abbreviation' => [
                'required',
                'string',
                'max:20',
                Rule::unique('units_of_measure', 'abbreviation')->ignore($unitId),
            ],
        ]);

        $payload = [
            'name'         => $this->form->name,
            'abbreviation' => strtoupper(trim($this->form->abbreviation)),
            'description'  => $this->form->description,
            'is_active'    => $this->form->is_active,
        ];

        if ($this->unit) {
            $this->unit->update($payload);

            return redirect()->route('units-of-measure.index')
                ->with('success', 'Unidade de medida atualizada com sucesso!');
        }

        UnitOfMeasure::create($payload);

        return redirect()->route('units-of-measure.index')
            ->with('success', 'Unidade de medida criada com sucesso!');
    }

    public function render()
    {
        $title = $this->unit ? 'Editar Unidade de Medida' : 'Nova Unidade de Medida';

        return view('livewire.cadastro.unidade-medida.form', [
            'isEditing' => (bool) $this->unit,
        ])->title($title);
    }
}

