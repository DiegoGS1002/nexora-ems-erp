<?php

namespace Database\Seeders;

use App\Enums\ProductionOrderStatus;
use App\Models\Product;
use App\Models\ProductionItem;
use App\Models\ProductionOrder;
use App\Models\ProductionOrderProduct;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductionOrderSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::where('is_active', true)->take(20)->get();
        $users    = User::where('is_active', true)->take(3)->get();

        if ($products->isEmpty()) {
            $this->command->warn('⚠️  Nenhum produto encontrado. Execute ProductSeeder antes.');
            return;
        }

        $responsavel = $users->isNotEmpty() ? $users->first() : null;

        $orders = [
            [
                'name'              => 'OP-2026-001 | Produção Lote Arroz Branco',
                'description'       => 'Produção do lote mensal de Arroz Branco Tipo 1 para reabastecimento do estoque.',
                'product_index'     => 0,
                'target_quantity'   => 500,
                'produced_quantity' => 500,
                'status'            => ProductionOrderStatus::Completed,
                'start_date'        => now()->subDays(30),
                'end_date'          => now()->subDays(22),
                'estimated_cost'    => 4875.00,
                'lot_number'        => 'LOTE-ARR-2026-04-001',
                'notes'             => 'Lote concluído dentro do prazo e sem perdas.',
                'components'        => [
                    ['product_index' => 1, 'required_qty' => 520, 'consumed_qty' => 510],
                    ['product_index' => 2, 'required_qty' => 10,  'consumed_qty' => 10],
                ],
                'order_products'    => [
                    ['product_index' => 0, 'target_quantity' => 300, 'produced_quantity' => 300],
                    ['product_index' => 1, 'target_quantity' => 200, 'produced_quantity' => 200],
                ],
            ],
            [
                'name'              => 'OP-2026-002 | Produção Molho de Tomate',
                'description'       => 'Produção da linha de molho de tomate para atendimento dos pedidos de venda pendentes.',
                'product_index'     => 2,
                'target_quantity'   => 1000,
                'produced_quantity' => 750,
                'status'            => ProductionOrderStatus::InProgress,
                'start_date'        => now()->subDays(5),
                'end_date'          => now()->addDays(3),
                'estimated_cost'    => 2200.00,
                'lot_number'        => 'LOTE-MLH-2026-04-002',
                'notes'             => 'Em andamento. 75% concluído.',
                'components'        => [
                    ['product_index' => 3,  'required_qty' => 800,  'consumed_qty' => 600],
                    ['product_index' => 4,  'required_qty' => 50,   'consumed_qty' => 40],
                    ['product_index' => 5,  'required_qty' => 100,  'consumed_qty' => 80],
                ],
                'order_products'    => [
                    ['product_index' => 2, 'target_quantity' => 600, 'produced_quantity' => 450],
                    ['product_index' => 3, 'target_quantity' => 400, 'produced_quantity' => 300],
                ],
            ],
            [
                'name'              => 'OP-2026-003 | Produção Kit Higiene Pessoal',
                'description'       => 'Montagem e embalagem de kits de higiene pessoal para o canal de varejo.',
                'product_index'     => 5,
                'target_quantity'   => 200,
                'produced_quantity' => 0,
                'status'            => ProductionOrderStatus::Planned,
                'start_date'        => now()->addDays(2),
                'end_date'          => now()->addDays(10),
                'estimated_cost'    => 8600.00,
                'lot_number'        => 'LOTE-KIT-2026-04-003',
                'notes'             => 'Aguardando liberação do estoque de insumos.',
                'components'        => [
                    ['product_index' => 6,  'required_qty' => 200, 'consumed_qty' => 0],
                    ['product_index' => 7,  'required_qty' => 200, 'consumed_qty' => 0],
                    ['product_index' => 8,  'required_qty' => 200, 'consumed_qty' => 0],
                    ['product_index' => 9,  'required_qty' => 200, 'consumed_qty' => 0],
                ],
                'order_products'    => [
                    ['product_index' => 5, 'target_quantity' => 200, 'produced_quantity' => 0],
                ],
            ],
            [
                'name'              => 'OP-2026-004 | Produção Bebidas Energéticas',
                'description'       => 'Produção e envase de bebidas energéticas para pedido de grande cliente.',
                'product_index'     => 10,
                'target_quantity'   => 2000,
                'produced_quantity' => 0,
                'status'            => ProductionOrderStatus::Paused,
                'start_date'        => now()->subDays(8),
                'end_date'          => now()->addDays(5),
                'estimated_cost'    => 13000.00,
                'lot_number'        => 'LOTE-BEB-2026-04-004',
                'notes'             => 'Pausada por falta de matéria-prima (aguardando entrega do fornecedor).',
                'components'        => [
                    ['product_index' => 11, 'required_qty' => 2000, 'consumed_qty' => 0],
                    ['product_index' => 12, 'required_qty' => 500,  'consumed_qty' => 0],
                ],
                'order_products'    => [
                    ['product_index' => 10, 'target_quantity' => 1200, 'produced_quantity' => 0],
                    ['product_index' => 11, 'target_quantity' => 800,  'produced_quantity' => 0],
                ],
            ],
            [
                'name'              => 'OP-2026-005 | Produção Lote Laticínios',
                'description'       => 'Produção e pasteurização de linha de laticínios para distribuição regional.',
                'product_index'     => 13,
                'target_quantity'   => 3000,
                'produced_quantity' => 3000,
                'status'            => ProductionOrderStatus::Completed,
                'start_date'        => now()->subDays(60),
                'end_date'          => now()->subDays(48),
                'estimated_cost'    => 9600.00,
                'lot_number'        => 'LOTE-LAT-2026-02-005',
                'notes'             => 'Concluído com aprovação do controle de qualidade.',
                'components'        => [
                    ['product_index' => 14, 'required_qty' => 6000, 'consumed_qty' => 5950],
                    ['product_index' => 15, 'required_qty' => 120,  'consumed_qty' => 115],
                ],
                'order_products'    => [
                    ['product_index' => 13, 'target_quantity' => 1800, 'produced_quantity' => 1800],
                    ['product_index' => 14, 'target_quantity' => 1200, 'produced_quantity' => 1200],
                ],
            ],
        ];

        $created = 0;
        foreach ($orders as $orderData) {
            $product    = $products->get($orderData['product_index'] % $products->count());
            $components = $orderData['components'];
            $orderProds = $orderData['order_products'];
            unset($orderData['components'], $orderData['order_products'], $orderData['product_index']);

            /** @var ProductionOrder $op */
            $op = ProductionOrder::create(array_merge($orderData, [
                'product_id' => $product?->id,
                'user_id'    => $responsavel?->id,
            ]));

            // Componentes / matéria-prima
            foreach ($components as $comp) {
                $compProduct = $products->get($comp['product_index'] % $products->count());
                ProductionItem::create([
                    'production_order_id' => $op->id,
                    'component_id'        => $compProduct?->id,
                    'required_qty'        => $comp['required_qty'],
                    'consumed_qty'        => $comp['consumed_qty'],
                ]);
            }

            // Produtos / saídas da ordem
            foreach ($orderProds as $oprod) {
                $opProduct = $products->get($oprod['product_index'] % $products->count());
                ProductionOrderProduct::create([
                    'production_order_id' => $op->id,
                    'product_id'          => $opProduct?->id,
                    'target_quantity'     => $oprod['target_quantity'],
                    'produced_quantity'   => $oprod['produced_quantity'],
                ]);
            }

            $created++;
        }

        $this->command->info("✅ {$created} ordens de produção semeadas.");
    }
}

