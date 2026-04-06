<?php

namespace Database\Factories;

use App\Enums\TipoPessoa;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'tipo_pessoa'        => TipoPessoa::PJ->value,
            'name'               => $this->faker->company(),
            'social_name'        => $this->faker->company() . ' LTDA',
            'taxNumber'          => $this->faker->numerify('##############'),
            'email'              => $this->faker->unique()->safeEmail(),
            'phone_number'       => $this->faker->numerify('(##) #####-####'),
            'address_zip_code'   => $this->faker->numerify('#####-###'),
            'address_street'     => $this->faker->streetName(),
            'address_number'     => $this->faker->buildingNumber(),
            'address_complement' => null,
            'address_district'   => $this->faker->word(),
            'address_city'       => $this->faker->city(),
            'address_state'      => $this->faker->stateAbbr(),
        ];
    }
}

