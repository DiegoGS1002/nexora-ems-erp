<?php

namespace Database\Factories;

use App\Enums\CategoriaVeiculo;
use App\Enums\CombustivelVeiculo;
use App\Enums\EspecieVeiculo;
use App\Enums\TipoVeiculo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    public function definition(): array
    {
        $brands = ['Toyota', 'Volkswagen', 'Fiat', 'Chevrolet', 'Honda', 'Ford', 'Hyundai', 'Mercedes', 'Renault', 'BMW'];
        $models = ['Corolla', 'Gol', 'Uno', 'Onix', 'HB20', 'Ka', 'HRV', 'Sprinter', 'Master', 'X5'];
        $colors = ['Branco', 'Prata', 'Preto', 'Cinza', 'Azul', 'Vermelho'];

        return [
            'plate'              => strtoupper(fake()->bothify('???#?##')),
            'renavam'            => fake()->unique()->numerify('###########'),
            'chassis'            => strtoupper(fake()->unique()->bothify('?????????????????')),
            'vehicle_type'       => fake()->randomElement(TipoVeiculo::cases())->value,
            'category'           => fake()->randomElement(CategoriaVeiculo::cases())->value,
            'species'            => fake()->randomElement(EspecieVeiculo::cases())->value,
            'manufacturing_year' => (string) fake()->numberBetween(2015, 2024),
            'model_year'         => (string) fake()->numberBetween(2016, 2025),
            'brand'              => fake()->randomElement($brands),
            'model'              => fake()->randomElement($models),
            'color'              => fake()->randomElement($colors),
            'fuel_type'          => fake()->randomElement(CombustivelVeiculo::cases())->value,
            'year'               => (string) fake()->numberBetween(2015, 2024),
            'is_active'          => true,
        ];
    }
}

