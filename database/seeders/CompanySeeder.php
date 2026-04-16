<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            [
                'name'                 => 'Nexora Alimentos',
                'social_name'          => 'Nexora Comercio de Alimentos LTDA',
                'cnpj'                 => '12.345.678/0001-90',
                'inscricao_estadual'   => '123.456.789.000',
                'inscricao_municipal'  => '987654321',
                'email'               => 'contato@nexora-alimentos.com.br',
                'phone'               => '(11) 3456-7890',
                'website'             => 'https://www.nexora-alimentos.com.br',
                'address_zip_code'    => '01310-100',
                'address_street'      => 'Avenida Paulista',
                'address_number'      => '1000',
                'address_complement'  => 'Andar 5',
                'address_district'    => 'Bela Vista',
                'address_city'        => 'São Paulo',
                'address_state'       => 'SP',
                'segment'             => 'Alimentos e Bebidas',
                'is_active'           => true,
                'notes'               => 'Empresa principal do grupo Nexora.',
            ],
            [
                'name'                => 'Nexora Logística',
                'social_name'         => 'Nexora Transportes e Logística LTDA',
                'cnpj'                => '23.456.789/0001-12',
                'inscricao_estadual'  => '234.567.890.000',
                'inscricao_municipal' => '876543210',
                'email'              => 'operacoes@nexora-logistica.com.br',
                'phone'              => '(21) 2345-6789',
                'website'            => 'https://www.nexora-logistica.com.br',
                'address_zip_code'   => '20040-020',
                'address_street'     => 'Rua da Assembléia',
                'address_number'     => '77',
                'address_complement' => 'Sala 302',
                'address_district'   => 'Centro',
                'address_city'       => 'Rio de Janeiro',
                'address_state'      => 'RJ',
                'segment'            => 'Transporte e Logística',
                'is_active'          => true,
                'notes'              => 'Responsável pela distribuição e frota.',
            ],
            [
                'name'                => 'Nexora Indústria',
                'social_name'         => 'Nexora Industria e Manufatura SA',
                'cnpj'                => '34.567.890/0001-34',
                'inscricao_estadual'  => '345.678.901.000',
                'inscricao_municipal' => '765432109',
                'email'              => 'producao@nexora-industria.com.br',
                'phone'              => '(31) 3333-4444',
                'website'            => 'https://www.nexora-industria.com.br',
                'address_zip_code'   => '30130-110',
                'address_street'     => 'Rua dos Caetés',
                'address_number'     => '500',
                'address_complement' => null,
                'address_district'   => 'Centro',
                'address_city'       => 'Belo Horizonte',
                'address_state'      => 'MG',
                'segment'            => 'Indústria e Manufatura',
                'is_active'          => true,
                'notes'              => 'Unidade fabril do grupo Nexora.',
            ],
            [
                'name'                => 'Nexora Varejo',
                'social_name'         => 'Nexora Comercio Varejista EIRELI',
                'cnpj'                => '45.678.901/0001-56',
                'inscricao_estadual'  => '456.789.012.000',
                'inscricao_municipal' => '654321098',
                'email'              => 'vendas@nexora-varejo.com.br',
                'phone'              => '(41) 3210-9876',
                'website'            => null,
                'address_zip_code'   => '80410-001',
                'address_street'     => 'Rua XV de Novembro',
                'address_number'     => '1200',
                'address_complement' => 'Loja 5',
                'address_district'   => 'Centro',
                'address_city'       => 'Curitiba',
                'address_state'      => 'PR',
                'segment'            => 'Varejo',
                'is_active'          => true,
                'notes'              => 'Ponto de venda ao consumidor final.',
            ],
        ];

        foreach ($companies as $company) {
            Company::firstOrCreate(
                ['cnpj' => $company['cnpj']],
                $company
            );
        }

        $this->command->info('✅ ' . count($companies) . ' empresas semeadas.');
    }
}

