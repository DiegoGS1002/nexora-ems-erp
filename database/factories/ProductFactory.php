<?php

namespace Database\Factories;

use App\Enums\NaturezaProduto;
use App\Enums\TipoProduto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'             => $this->faker->unique()->words(3, true),
            'ean'              => (string) $this->faker->numerify('#############'),
            'description'      => $this->faker->sentence(),
            'short_description'=> $this->faker->sentence(8),
            'brand'            => $this->faker->company(),
            'product_type'     => TipoProduto::Fisico->value,
            'nature'           => NaturezaProduto::MercadoriaRevenda->value,
            'unit_of_measure'  => 'UN',
            'category'         => 'informatica',
            'sale_price'       => $this->faker->randomFloat(2, 10, 5000),
            'stock'            => $this->faker->numberBetween(0, 500),
            'is_active'        => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function servico(): static
    {
        return $this->state(fn () => ['product_type' => TipoProduto::Servico->value]);
    }
}

