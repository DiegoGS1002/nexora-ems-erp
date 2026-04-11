<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StockMovement;
use App\Models\Product;
use App\Models\User;

class StockMovementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::first();

        if (!$admin) {
            $this->command->warn('Nenhum usuário encontrado. Execute o UserSeeder primeiro.');
            return;
        }

        $products = Product::limit(10)->get();

        if ($products->isEmpty()) {
            $this->command->warn('Nenhum produto encontrado. Execute o ProductSeeder primeiro.');
            return;
        }

        $movements = [];
        $types = ['input', 'output', 'adjustment', 'transfer'];
        $origins = [
            'input' => ['Compra #1001', 'Compra #1002', 'Devolução Cliente', 'Transferência CD'],
            'output' => ['Venda #2001', 'Venda #2002', 'Perda', 'Transferência Filial'],
            'adjustment' => ['Inventário', 'Correção Manual', 'Ajuste de Balanço'],
            'transfer' => ['Transferência Interna', 'Mudança de Setor', 'Realocação'],
        ];

        foreach ($products as $product) {
            // Adicionar algumas movimentações para cada produto
            for ($i = 0; $i < rand(3, 8); $i++) {
                $type = $types[array_rand($types)];
                $origin = $origins[$type][array_rand($origins[$type])];

                $movements[] = [
                    'product_id' => $product->id,
                    'user_id' => $admin->id,
                    'quantity' => rand(1, 100) + (rand(0, 999) / 1000),
                    'type' => $type,
                    'origin' => $origin,
                    'unit_cost' => $type === 'input' ? rand(10, 500) + (rand(0, 99) / 100) : null,
                    'observation' => $type === 'adjustment' ? 'Ajuste realizado durante inventário mensal' : null,
                    'created_at' => now()->subDays(rand(0, 60)),
                    'updated_at' => now()->subDays(rand(0, 60)),
                ];
            }
        }

        StockMovement::insert($movements);

        $this->command->info('✅ ' . count($movements) . ' movimentações de estoque criadas com sucesso!');
    }
}
