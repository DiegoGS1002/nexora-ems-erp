<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        return [
            'social_name'        => $this->faker->company() . ' LTDA',
            'name'               => $this->faker->name(),
            'taxNumber'          => $this->faker->numerify('##############'),
            'email'              => $this->faker->unique()->safeEmail(),
            'phone_number'       => $this->faker->numerify('(##) #####-####'),
            'address_zip_code'   => $this->faker->numerify('#####-###'),
            'address_street'     => $this->faker->streetName(),
            'address_number'     => $this->faker->buildingNumber(),
            'address_complement' => '',
            'address_district'   => $this->faker->word(),
            'address_city'       => $this->faker->city(),
            'address_state'      => $this->faker->stateAbbr(),
        ];
    }
}

