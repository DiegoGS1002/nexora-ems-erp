<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'social_name' => fake()->company().' Ltda',
            'cnpj' => fake()->unique()->numerify('##############'),
            'address_state' => 'SP',
            'is_active' => true,
        ];
    }
}
