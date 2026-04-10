<?php

namespace App\Models;

use App\Enums\CategoriaVeiculo;
use App\Enums\CombustivelVeiculo;
use App\Enums\EspecieVeiculo;
use App\Enums\TipoVeiculo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'plate',
        'renavam',
        'chassis',
        'vehicle_type',
        'category',
        'species',
        'manufacturing_year',
        'model_year',
        'brand',
        'model',
        'color',
        'fuel_type',
        'year',
        'power_hp',
        'displacement_cc',
        'doors',
        'passenger_capacity',
        'transmission_type',
        'traction_type',
        'gross_weight',
        'net_weight',
        'cargo_capacity',
        'department',
        'responsible_driver',
        'cost_center',
        'unit',
        'current_location',
        'location_note',
        'is_active',
        'acquisition_date',
        'acquisition_value',
        'photos',
        'observations',
    ];

    protected $casts = [
        'vehicle_type'      => TipoVeiculo::class,
        'category'          => CategoriaVeiculo::class,
        'species'           => EspecieVeiculo::class,
        'fuel_type'         => CombustivelVeiculo::class,
        'is_active'         => 'boolean',
        'acquisition_date'  => 'date',
        'acquisition_value' => 'decimal:2',
        'gross_weight'      => 'decimal:2',
        'net_weight'        => 'decimal:2',
        'cargo_capacity'    => 'decimal:2',
        'photos'            => 'array',
    ];

    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? 'Ativo' : 'Inativo';
    }

    public function getDisplayNameAttribute(): string
    {
        return trim(implode(' ', array_filter([$this->brand, $this->model]))) ?: ($this->name ?? 'Veículo');
    }
}

