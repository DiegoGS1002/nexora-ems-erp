<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Arroz Branco Tipo 1 5kg',
                'ean' => '7891000100101',
                'description' => 'Pacote de arroz branco tipo 1 com 5kg.',
                'unit_of_measure' => 'KG',
                'sale_price' => 27.90,
                'stock' => 120,
                'expiration_date' => now()->addMonths(10)->toDateString(),
                'category' => 'alimentos',
                'image' => null,
            ],
            [
                'name' => 'Feijao Carioca 1kg',
                'ean' => '7891000100102',
                'description' => 'Feijao carioca selecionado em pacote de 1kg.',
                'unit_of_measure' => 'KG',
                'sale_price' => 8.50,
                'stock' => 200,
                'expiration_date' => now()->addMonths(8)->toDateString(),
                'category' => 'alimentos',
                'image' => null,
            ],
            [
                'name' => 'Detergente Neutro 500ml',
                'ean' => '7891000100103',
                'description' => 'Detergente liquido neutro para loucas.',
                'unit_of_measure' => 'UN',
                'sale_price' => 2.99,
                'stock' => 350,
                'expiration_date' => null,
                'category' => 'outro',
                'image' => null,
            ],
            [
                'name' => 'Papel Higienico Folha Dupla 12un',
                'ean' => '7891000100104',
                'description' => 'Fardo com 12 rolos de papel higienico folha dupla.',
                'unit_of_measure' => 'CX',
                'sale_price' => 19.90,
                'stock' => 90,
                'expiration_date' => null,
                'category' => 'outro',
                'image' => null,
            ],
            [
                'name' => 'Sabonete em Barra 90g',
                'ean' => '7891000100105',
                'description' => 'Sabonete em barra perfumado de 90g.',
                'unit_of_measure' => 'UN',
                'sale_price' => 1.99,
                'stock' => 500,
                'expiration_date' => now()->addMonths(18)->toDateString(),
                'category' => 'outro',
                'image' => null,
            ],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(
                ['ean' => $product['ean']],
                ['id' => (string) Str::uuid(), ...$product]
            );
        }
    }
}

