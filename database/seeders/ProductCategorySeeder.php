<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Hortifruti',               'slug' => 'hortifruti',              'color' => '#10B981', 'description' => 'Frutas, legumes, verduras e temperos frescos'],
            ['name' => 'Carnes e Peixes',           'slug' => 'carnes-e-peixes',         'color' => '#EF4444', 'description' => 'Carnes bovinas, suínas, aves, peixes e frutos do mar'],
            ['name' => 'Padaria e Confeitaria',     'slug' => 'padaria-confeitaria',     'color' => '#F59E0B', 'description' => 'Pães, bolos, doces, salgados e produtos de confeitaria'],
            ['name' => 'Laticínios e Frios',        'slug' => 'laticinios-frios',        'color' => '#3B82F6', 'description' => 'Leite, queijos, iogurtes, manteiga, frios e embutidos'],
            ['name' => 'Mercearia Seca',            'slug' => 'mercearia-seca',          'color' => '#8B5CF6', 'description' => 'Arroz, feijão, macarrão, farinhas, óleos e temperos'],
            ['name' => 'Bebidas',                   'slug' => 'bebidas',                 'color' => '#06B6D4', 'description' => 'Água, sucos, refrigerantes, cervejas, vinhos e destilados'],
            ['name' => 'Higiene Pessoal',           'slug' => 'higiene-pessoal',         'color' => '#EC4899', 'description' => 'Shampoo, sabonete, creme dental, desodorante e cuidados pessoais'],
            ['name' => 'Limpeza e Conservação',     'slug' => 'limpeza-conservacao',     'color' => '#14B8A6', 'description' => 'Detergentes, desinfetantes, alvejantes e produtos de limpeza geral'],
            ['name' => 'Congelados e Refrigerados', 'slug' => 'congelados-refrigerados', 'color' => '#6366F1', 'description' => 'Refeições prontas congeladas, sorvetes, pizzas e massas frescas'],
            ['name' => 'Bazar e Utilidades',        'slug' => 'bazar-utilidades',        'color' => '#F97316', 'description' => 'Utensílios domésticos, pilhas, descartáveis e papelaria'],
            ['name' => 'Bebê e Criança',            'slug' => 'bebe-crianca',            'color' => '#FBBF24', 'description' => 'Fraldas, papinhas, leite infantil e produtos para bebês'],
            ['name' => 'Pet Shop',                  'slug' => 'pet-shop',                'color' => '#A78BFA', 'description' => 'Ração, petiscos, acessórios e higiene para animais de estimação'],
            ['name' => 'Padaria (Produção Própria)', 'slug' => 'padaria-producao-propria', 'color' => '#D97706', 'description' => 'Produtos fabricados na padaria interna do supermercado'],
            ['name' => 'Açougue',                   'slug' => 'acougue',                 'color' => '#DC2626', 'description' => 'Cortes especiais e processados do açougue interno'],
        ];

        foreach ($categories as $data) {
            ProductCategory::firstOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, ['is_active' => true])
            );
        }
    }
}

