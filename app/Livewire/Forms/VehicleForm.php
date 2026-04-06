<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class VehicleForm extends Form
{
    // ── Identificação ─────────────────────────────────
    #[Validate('required|string|max:20')]
    public string $plate = '';

    #[Validate('required|string|max:11')]
    public string $renavam = '';

    #[Validate('required|string|size:17')]
    public string $chassis = '';

    #[Validate('required|string')]
    public string $vehicle_type = '';

    #[Validate('required|string')]
    public string $category = '';

    #[Validate('required|string')]
    public string $species = '';

    #[Validate('required|string|max:4')]
    public string $manufacturing_year = '';

    #[Validate('required|string|max:4')]
    public string $model_year = '';

    #[Validate('required|string|max:100')]
    public string $brand = '';

    #[Validate('required|string|max:100')]
    public string $model = '';

    #[Validate('required|string')]
    public string $color = '';

    #[Validate('required|string')]
    public string $fuel_type = '';

    // ── Informações Técnicas ──────────────────────────
    #[Validate('nullable|string|max:10')]
    public ?string $power_hp = null;

    #[Validate('nullable|string|max:10')]
    public ?string $displacement_cc = null;

    #[Validate('nullable|integer|min:1|max:10')]
    public ?string $doors = null;

    #[Validate('nullable|integer|min:1')]
    public ?string $passenger_capacity = null;

    #[Validate('nullable|string')]
    public ?string $transmission_type = null;

    #[Validate('nullable|string')]
    public ?string $traction_type = null;

    #[Validate('nullable|numeric|min:0')]
    public ?string $gross_weight = null;

    #[Validate('nullable|numeric|min:0')]
    public ?string $net_weight = null;

    #[Validate('nullable|numeric|min:0')]
    public ?string $cargo_capacity = null;

    // ── Vinculação e Localização ──────────────────────
    #[Validate('nullable|string|max:100')]
    public ?string $department = null;

    #[Validate('nullable|string|max:150')]
    public ?string $responsible_driver = null;

    #[Validate('nullable|string|max:100')]
    public ?string $cost_center = null;

    #[Validate('nullable|string|max:100')]
    public ?string $unit = null;

    #[Validate('nullable|string|max:150')]
    public ?string $current_location = null;

    #[Validate('nullable|string|max:255')]
    public ?string $location_note = null;

    // ── Status e Aquisição ────────────────────────────
    #[Validate('boolean')]
    public bool $is_active = true;

    #[Validate('nullable|date')]
    public ?string $acquisition_date = null;

    #[Validate('nullable|numeric|min:0')]
    public ?string $acquisition_value = null;

    // ── Observações ───────────────────────────────────
    #[Validate('nullable|string')]
    public ?string $observations = null;

    // ── Apelido / Nome ────────────────────────────────
    #[Validate('nullable|string|max:100')]
    public ?string $name = null;
}

