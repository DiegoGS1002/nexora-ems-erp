<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use Illuminate\Support\Str;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Fornecedor Alpha LTDA',
                'social_name' => 'Alpha Distribuidora',
                'taxNumber' => '12345678000190',
                'email' => 'contato@alpha.com',
                'address_zip_code' => '01001000',
                'address_street' => 'Rua das Flores',
                'address_number' => '123',
                'address_complement' => 'Sala 01',
                'address_district' => 'Centro',
                'address_city' => 'São Paulo',
                'address_state' => 'SP',
                'phone_number' => '11999991111',
            ],
            [
                'name' => 'Fornecedor Beta LTDA',
                'social_name' => 'Beta Atacadista',
                'taxNumber' => '98765432000155',
                'email' => 'vendas@beta.com',
                'address_zip_code' => '20040002',
                'address_street' => 'Avenida Central',
                'address_number' => '456',
                'address_complement' => null,
                'address_district' => 'Centro',
                'address_city' => 'Rio de Janeiro',
                'address_state' => 'RJ',
                'phone_number' => '21988882222',
            ],
            [
                'name' => 'Fornecedor Gama LTDA',
                'social_name' => 'Gama Comercial',
                'taxNumber' => '45123789000110',
                'email' => 'gama@gama.com',
                'address_zip_code' => '30140071',
                'address_street' => 'Rua Minas Gerais',
                'address_number' => '789',
                'address_complement' => 'Loja B',
                'address_district' => 'Funcionários',
                'address_city' => 'Belo Horizonte',
                'address_state' => 'MG',
                'phone_number' => '31977773333',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
