<?php

namespace Database\Seeders;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\User;
use App\Models\Carrier;
use Illuminate\Database\Seeder;

class PurchaseOrderSeeder extends Seeder
{
    public function run(): void
    {
        if (PurchaseOrder::count() > 0) {
            $this->command->warn('Pedidos de compra já existem. Seeder ignorado.');
            return;
        }

        $suppliers = Supplier::take(3)->get();
        $products  = Product::take(10)->get();
        $admin     = User::where('is_admin', true)->first() ?? User::first();
        $carrier   = Carrier::first();

        if ($suppliers->isEmpty() || $products->isEmpty()) {
            $this->command->warn('Fornecedores/Produtos não encontrados. Execute os seeders anteriores primeiro.');
            return;
        }

        $orders = [
            [
                'order_number'          => 'PC-2026-001',
                'supplier_id'           => $suppliers->get(0)?->id,
                'buyer_id'              => $admin?->id,
                'status'                => 'aprovado',
                'origin'                => 'manual',
                'order_date'            => '2026-05-05 09:00:00',
                'expected_delivery_date'=> '2026-05-20',
                'received_at'           => '2026-05-19',
                'payment_condition'     => '30 dias',
                'payment_method'        => 'boleto',
                'freight_type'          => 'CIF',
                'carrier_id'            => $carrier?->id,
                'delivery_address'      => 'Rua Principal, 500 - Centro - Belo Horizonte/MG',
                'subtotal'              => 18000.00,
                'discount_amount'       => 500.00,
                'shipping_amount'       => 200.00,
                'other_expenses'        => 0.00,
                'total_amount'          => 17700.00,
                'notes'                 => 'Pedido urgente para reposição de estoque.',
                'approved_by'           => $admin?->id,
                'approved_at'           => '2026-05-05 14:00:00',
                'created_by'            => $admin?->id,
                'items' => [
                    ['qty' => 100, 'unit_price' => 50.00,  'description' => null, 'product_index' => 0],
                    ['qty' => 200, 'unit_price' => 42.50,  'description' => null, 'product_index' => 1],
                    ['qty' => 50,  'unit_price' => 85.00,  'description' => null, 'product_index' => 2],
                ],
            ],
            [
                'order_number'          => 'PC-2026-002',
                'supplier_id'           => $suppliers->get(1)?->id ?? $suppliers->get(0)->id,
                'buyer_id'              => $admin?->id,
                'status'                => 'rascunho',
                'origin'                => 'solicitacao',
                'order_date'            => '2026-05-15 10:00:00',
                'expected_delivery_date'=> '2026-06-05',
                'payment_condition'     => '15/30 dias',
                'payment_method'        => 'pix',
                'freight_type'          => 'FOB',
                'subtotal'              => 9500.00,
                'discount_amount'       => 0.00,
                'shipping_amount'       => 450.00,
                'other_expenses'        => 0.00,
                'total_amount'          => 9950.00,
                'notes'                 => 'Aguardando aprovação do gerente.',
                'created_by'            => $admin?->id,
                'items' => [
                    ['qty' => 80,  'unit_price' => 65.00,  'description' => null, 'product_index' => 3],
                    ['qty' => 30,  'unit_price' => 120.00, 'description' => null, 'product_index' => 4],
                ],
            ],
            [
                'order_number'          => 'PC-2026-003',
                'supplier_id'           => $suppliers->get(2)?->id ?? $suppliers->get(0)->id,
                'buyer_id'              => $admin?->id,
                'status'                => 'recebido_total',
                'origin'                => 'cotacao',
                'order_date'            => '2026-04-20 08:30:00',
                'expected_delivery_date'=> '2026-05-05',
                'received_at'           => '2026-05-04',
                'payment_condition'     => 'À vista',
                'payment_method'        => 'transferencia',
                'freight_type'          => 'CIF',
                'carrier_id'            => $carrier?->id,
                'delivery_address'      => 'Rua Principal, 500 - Centro - Belo Horizonte/MG',
                'subtotal'              => 32000.00,
                'discount_amount'       => 1000.00,
                'shipping_amount'       => 0.00,
                'other_expenses'        => 0.00,
                'total_amount'          => 31000.00,
                'notes'                 => 'Pedido de grande volume com desconto negociado.',
                'approved_by'           => $admin?->id,
                'approved_at'           => '2026-04-21 09:00:00',
                'created_by'            => $admin?->id,
                'items' => [
                    ['qty' => 500, 'unit_price' => 28.00, 'description' => null, 'product_index' => 5],
                    ['qty' => 200, 'unit_price' => 55.00, 'description' => null, 'product_index' => 6],
                    ['qty' => 100, 'unit_price' => 90.00, 'description' => null, 'product_index' => 7],
                ],
            ],
        ];

        foreach ($orders as $orderData) {
            $items = $orderData['items'];
            unset($orderData['items']);

            $order = PurchaseOrder::create($orderData);

            foreach ($items as $item) {
                $product = $products->get($item['product_index'] % $products->count());
                $qty     = $item['qty'];
                $price   = $item['unit_price'];
                $total   = round($qty * $price, 2);

                PurchaseOrderItem::create([
                    'purchase_order_id' => $order->id,
                    'product_id'        => $product?->id,
                    'description'       => $product?->name ?? 'Produto',
                    'sku'               => $product?->sku ?? null,
                    'unit'              => $product?->unit ?? 'UN',
                    'quantity'          => $qty,
                    'unit_price'        => $price,
                    'discount'          => 0,
                    'total'             => $total,
                ]);
            }
        }

        $this->command->info('✅ ' . count($orders) . ' pedidos de compra criados com itens.');
    }
}

