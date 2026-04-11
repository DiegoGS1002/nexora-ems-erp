<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Carrier;

class CarrierSeeder extends Seeder
{
    public function run(): void
    {
        $carriers = [
            [
                'name' => 'Correios',
                'trade_name' => 'Empresa Brasileira de Correios e Telégrafos',
                'cnpj' => '34028316000103',
                'ie' => 'ISENTO',
                'phone' => '3003-0100',
                'email' => 'atendimento@correios.com.br',
                'address' => 'Brasília - DF',
                'is_active' => true,
            ],
            [
                'name' => 'Jadlog',
                'trade_name' => 'Jadlog Logística',
                'cnpj' => '04884082000178',
                'ie' => null,
                'phone' => '0800-705-3040',
                'email' => 'sac@jadlog.com.br',
                'address' => 'Barueri - SP',
                'is_active' => true,
            ],
            [
                'name' => 'Total Express',
                'trade_name' => 'Total Express Transportes Urgentes Ltda',
                'cnpj' => '02421421000111',
                'ie' => null,
                'phone' => '0800-728-2800',
                'email' => 'sac@totalexpress.com.br',
                'address' => 'São Paulo - SP',
                'is_active' => true,
            ],
            [
                'name' => 'Transportadora Própria',
                'trade_name' => 'Frota Própria',
                'cnpj' => null,
                'ie' => null,
                'phone' => null,
                'email' => null,
                'address' => 'Local',
                'is_active' => true,
            ],
            [
                'name' => 'Retirada no Local',
                'trade_name' => 'Cliente Retira',
                'cnpj' => null,
                'ie' => null,
                'phone' => null,
                'email' => null,
                'address' => null,
                'is_active' => true,
            ],
        ];

        foreach ($carriers as $carrier) {
            Carrier::create($carrier);
        }

        $this->command->info('Transportadoras criadas com sucesso!');
    }
}

