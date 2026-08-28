<?php

namespace Database\Factories;

use App\Enums\FiscalNoteStatus;
use App\Enums\FiscalConsumer;
use App\Enums\FiscalEmissionType;
use App\Enums\FiscalOperationType;
use App\Enums\FiscalPresence;
use App\Enums\FiscalPurpose;
use App\Enums\FiscalFreightMode;
use Illuminate\Database\Eloquent\Factories\Factory;

class FiscalNoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'invoice_number' => fake()->unique()->numerify('#########'),
            'series' => '1',
            'type' => 'nfe',
            'environment' => 'homologation',
            'status' => FiscalNoteStatus::Draft,
            'amount' => 0,
            'operation_nature' => 'Venda de Mercadoria',
            'operation_type' => FiscalOperationType::Sale,
            'consumer_type' => FiscalConsumer::Final,
            'presence_type' => FiscalPresence::InPerson,
            'purpose' => FiscalPurpose::Normal,
            'emission_type' => FiscalEmissionType::Normal,
            'freight_mode' => FiscalFreightMode::None,
        ];
    }
}
