<?php

namespace Database\Seeders;

use App\Enums\CanalVenda;
use App\Enums\OrigemPedido;
use App\Enums\SalesOrderStatus;
use App\Enums\TipoFrete;
use App\Enums\TipoOperacaoVenda;
use App\Models\Client;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class SalesOrderSeeder extends Seeder
{
    public function run(): void
    {
        $clients  = Client::all();
        $products = Product::where('is_active', true)->take(20)->get();
        $sellers  = User::where('is_active', true)->take(3)->get();

        if ($clients->isEmpty() || $products->isEmpty()) {
            $this->command->warn('⚠️  Nenhum cliente ou produto encontrado. Execute ClientSeeder e ProductSeeder antes.');
            return;
        }

        $orders = [
            [
                'status'         => SalesOrderStatus::Approved,
                'operation_type' => TipoOperacaoVenda::VendaNormal,
                'sales_channel'  => CanalVenda::Balcao,
                'origin'         => OrigemPedido::Manual,
                'freight_type'   => TipoFrete::CIF,
                'order_date'     => now()->subDays(20),
                'delivery_date'  => now()->subDays(10)->toDateString(),
                'payment_condition' => '30/60 dias',
                'shipping_amount'   => 80.00,
                'internal_notes'    => 'Cliente prioritário. Entrega confirmada.',
                'client_index'      => 0,
                'items'             => [
                    ['product_index' => 0, 'quantity' => 10, 'unit_price' => null],
                    ['product_index' => 1, 'quantity' => 5,  'unit_price' => null],
                    ['product_index' => 2, 'quantity' => 20, 'unit_price' => null],
                ],
            ],
            [
                'status'         => SalesOrderStatus::Delivered,
                'operation_type' => TipoOperacaoVenda::VendaNormal,
                'sales_channel'  => CanalVenda::Online,
                'origin'         => OrigemPedido::Ecommerce,
                'freight_type'   => TipoFrete::FOB,
                'order_date'     => now()->subDays(35),
                'delivery_date'  => now()->subDays(22)->toDateString(),
                'payment_condition' => 'À vista',
                'shipping_amount'   => 0.00,
                'internal_notes'    => 'Pedido de e-commerce, pagamento antecipado.',
                'client_index'      => 1,
                'items'             => [
                    ['product_index' => 3, 'quantity' => 8,  'unit_price' => null],
                    ['product_index' => 4, 'quantity' => 15, 'unit_price' => null],
                ],
            ],
            [
                'status'         => SalesOrderStatus::Aberto,
                'operation_type' => TipoOperacaoVenda::VendaNormal,
                'sales_channel'  => CanalVenda::Representante,
                'origin'         => OrigemPedido::ForcaVendas,
                'freight_type'   => TipoFrete::CIF,
                'order_date'     => now()->subDays(5),
                'delivery_date'  => now()->addDays(10)->toDateString(),
                'payment_condition' => '28 dias',
                'shipping_amount'   => 150.00,
                'internal_notes'    => 'Pedido via representante regional.',
                'client_index'      => 2,
                'items'             => [
                    ['product_index' => 5, 'quantity' => 50, 'unit_price' => null],
                    ['product_index' => 6, 'quantity' => 30, 'unit_price' => null],
                    ['product_index' => 7, 'quantity' => 12, 'unit_price' => null],
                ],
            ],
            [
                'status'         => SalesOrderStatus::EmSeparacao,
                'operation_type' => TipoOperacaoVenda::VendaNormal,
                'sales_channel'  => CanalVenda::Televendas,
                'origin'         => OrigemPedido::Manual,
                'freight_type'   => TipoFrete::CIF,
                'order_date'     => now()->subDays(3),
                'delivery_date'  => now()->addDays(7)->toDateString(),
                'payment_condition' => '30 dias',
                'shipping_amount'   => 60.00,
                'internal_notes'    => 'Separação iniciada em 13/04.',
                'client_index'      => 3,
                'items'             => [
                    ['product_index' => 8,  'quantity' => 6,  'unit_price' => null],
                    ['product_index' => 9,  'quantity' => 24, 'unit_price' => null],
                ],
            ],
            [
                'status'         => SalesOrderStatus::Draft,
                'operation_type' => TipoOperacaoVenda::Consignacao,
                'sales_channel'  => CanalVenda::Balcao,
                'origin'         => OrigemPedido::Manual,
                'freight_type'   => TipoFrete::Terceiros,
                'order_date'     => now()->subDay(),
                'delivery_date'  => now()->addDays(15)->toDateString(),
                'payment_condition' => '60 dias',
                'shipping_amount'   => 200.00,
                'internal_notes'    => 'Rascunho aguardando aprovação do gerente.',
                'client_index'      => 4,
                'items'             => [
                    ['product_index' => 10, 'quantity' => 100, 'unit_price' => null],
                    ['product_index' => 11, 'quantity' => 50,  'unit_price' => null],
                    ['product_index' => 12, 'quantity' => 25,  'unit_price' => null],
                ],
            ],
            [
                'status'         => SalesOrderStatus::Cancelled,
                'operation_type' => TipoOperacaoVenda::VendaNormal,
                'sales_channel'  => CanalVenda::Online,
                'origin'         => OrigemPedido::Ecommerce,
                'freight_type'   => TipoFrete::FOB,
                'order_date'     => now()->subDays(45),
                'delivery_date'  => now()->subDays(30)->toDateString(),
                'payment_condition' => 'À vista',
                'shipping_amount'   => 0.00,
                'internal_notes'    => 'Cancelado a pedido do cliente por duplicidade.',
                'client_index'      => 0,
                'items'             => [
                    ['product_index' => 0, 'quantity' => 3, 'unit_price' => null],
                ],
            ],
        ];

        $created = 0;
        foreach ($orders as $orderData) {
            $client    = $clients->get($orderData['client_index'] % $clients->count());
            $seller    = $sellers->isNotEmpty() ? $sellers->get($created % $sellers->count()) : null;
            $itemsData = $orderData['items'];
            unset($orderData['items'], $orderData['client_index']);

            $nextId      = (SalesOrder::max('id') ?? 0) + 1;
            $orderNumber = 'PV-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);

            /** @var SalesOrder $order */
            $order = SalesOrder::create(array_merge($orderData, [
                'order_number'      => $orderNumber,
                'client_id'         => $client?->id,
                'user_id'           => $seller?->id,
                'seller_id'         => $seller?->id,
                'client_cpf_cnpj'   => $client?->taxNumber,
                'client_type'       => $client?->tipo_pessoa?->value ?? 'PJ',
                'client_situation'  => $client?->situation ?? 'active',
                'subtotal'          => 0,
                'gross_total'       => 0,
                'discount_amount'   => 0,
                'additions_amount'  => 0,
                'insurance_amount'  => 0,
                'other_expenses'    => 0,
                'net_total'         => 0,
                'total_amount'      => 0,
                'is_fiscal'         => false,
                'needs_approval'    => false,
            ]));

            $grossTotal = 0;
            foreach ($itemsData as $itemData) {
                $productIndex = $itemData['product_index'] % $products->count();
                $product      = $products->get($productIndex);
                $qty          = $itemData['quantity'];
                $price        = $itemData['unit_price'] ?? (float) $product->sale_price;
                $subtotal     = $qty * $price;
                $grossTotal  += $subtotal;

                SalesOrderItem::create([
                    'sales_order_id' => $order->id,
                    'product_id'     => $product->id,
                    'sku'            => $product->product_code,
                    'ean'            => $product->ean,
                    'description'    => $product->name,
                    'unit'           => $product->unit_of_measure,
                    'quantity'       => $qty,
                    'unit_price'     => $price,
                    'discount'       => 0,
                    'discount_percent'=> 0,
                    'addition'       => 0,
                    'addition_percent'=> 0,
                    'tax_amount'     => 0,
                    'subtotal'       => $subtotal,
                    'total'          => $subtotal,
                ]);
            }

            $netTotal   = $grossTotal - 0 + 0; // sem desconto/acréscimo
            $totalAmount = $netTotal + (float) ($orderData['shipping_amount'] ?? 0);

            $order->update([
                'subtotal'     => $grossTotal,
                'gross_total'  => $grossTotal,
                'net_total'    => $netTotal,
                'total_amount' => $totalAmount,
            ]);

            $created++;
        }

        $this->command->info("✅ {$created} pedidos de venda semeados.");
    }
}


