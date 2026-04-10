<?php

namespace Database\Seeders;

use App\Models\UnitOfMeasure;
use Illuminate\Database\Seeder;

class UnitOfMeasureSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['abbreviation' => 'UN',  'name' => 'Unidade',        'description' => 'Unidade simples de contagem'],
            ['abbreviation' => 'CX',  'name' => 'Caixa',          'description' => 'Agrupamento em caixa'],
            ['abbreviation' => 'KG',  'name' => 'Quilograma',     'description' => 'Medida de massa em quilogramas'],
            ['abbreviation' => 'G',   'name' => 'Grama',          'description' => 'Medida de massa em gramas'],
            ['abbreviation' => 'LT',  'name' => 'Litro',          'description' => 'Medida de volume em litros'],
            ['abbreviation' => 'ML',  'name' => 'Mililitro',      'description' => 'Medida de volume em mililitros'],
            ['abbreviation' => 'MT',  'name' => 'Metro',          'description' => 'Medida de comprimento em metros'],
            ['abbreviation' => 'CM',  'name' => 'Centímetro',     'description' => 'Medida de comprimento em centímetros'],
            ['abbreviation' => 'M2',  'name' => 'Metro Quadrado', 'description' => 'Medida de área em metros quadrados'],
            ['abbreviation' => 'PC',  'name' => 'Peça',           'description' => 'Peça avulsa de produto'],
            ['abbreviation' => 'PR',  'name' => 'Par',            'description' => 'Par de itens (ex: sapatos, meias)'],
            ['abbreviation' => 'DZ',  'name' => 'Dúzia',          'description' => 'Conjunto de 12 unidades'],
            ['abbreviation' => 'CT',  'name' => 'Cento',          'description' => 'Conjunto de 100 unidades'],
            ['abbreviation' => 'PCT', 'name' => 'Pacote',         'description' => 'Produto vendido em pacote'],
            ['abbreviation' => 'RL',  'name' => 'Rolo',           'description' => 'Produto vendido em rolo (ex: plástico, tecido)'],
            ['abbreviation' => 'SC',  'name' => 'Saco',           'description' => 'Produto vendido em saco'],
            ['abbreviation' => 'TB',  'name' => 'Tambor',         'description' => 'Produto vendido em tambor'],
            ['abbreviation' => 'H',   'name' => 'Hora',           'description' => 'Unidade de tempo para serviços'],
        ];

        foreach ($units as $data) {
            UnitOfMeasure::firstOrCreate(
                ['abbreviation' => $data['abbreviation']],
                array_merge($data, [
                    'id' => (string) \Illuminate\Support\Str::uuid(),
                    'is_active' => true
                ])
            );
        }
    }
}

