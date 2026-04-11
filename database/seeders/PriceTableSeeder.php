<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PriceTable;
use App\Models\PriceTableItem;
use App\Models\Product;

class PriceTableSeeder extends Seeder
{
    public function run(): void
    {
        // Tabela de Preço Varejo
        $varejo = PriceTable::create([
            'name' => 'Tabela Varejo',
            'code' => 'VAR-001',
            'description' => 'Tabela de preços para venda no varejo',
            'is_active' => true,
            'is_default' => true,
            'valid_from' => now(),
            'valid_until' => now()->addYear(),
        ]);

        // Tabela de Preço Atacado
        $atacado = PriceTable::create([
            'name' => 'Tabela Atacado',
            'code' => 'ATK-001',
            'description' => 'Tabela de preços para venda no atacado',
            'is_active' => true,
            'is_default' => false,
            'valid_from' => now(),
            'valid_until' => now()->addMonths(6),
        ]);

        // Tabela Promocional
        $promocional = PriceTable::create([
            'name' => 'Black Friday 2026',
            'code' => 'PROMO-BF2026',
            'description' => 'Promoção Black Friday',
            'is_active' => true,
            'is_default' => false,
            'valid_from' => now(),
            'valid_until' => now()->addDays(7),
        ]);

        // Buscar produtos para adicionar preços
        $products = Product::limit(10)->get();

        foreach ($products as $product) {
            $basePrice = $product->sale_price ?? $product->price ?? 100.00;

            // Preço Varejo (preço normal)
            PriceTableItem::create([
                'price_table_id' => $varejo->id,
                'product_id' => $product->id,
                'price' => $basePrice,
                'minimum_price' => $basePrice * 0.90, // 10% de desconto máximo
            ]);

            // Preço Atacado (15% mais barato)
            PriceTableItem::create([
                'price_table_id' => $atacado->id,
                'product_id' => $product->id,
                'price' => $basePrice * 0.85,
                'minimum_price' => $basePrice * 0.75,
            ]);

            // Preço Promocional (30% desconto)
            PriceTableItem::create([
                'price_table_id' => $promocional->id,
                'product_id' => $product->id,
                'price' => $basePrice,
                'promotional_price' => $basePrice * 0.70,
                'promotional_valid_from' => now(),
                'promotional_valid_until' => now()->addDays(7),
            ]);

            // Preço por quantidade (Atacado)
            PriceTableItem::create([
                'price_table_id' => $atacado->id,
                'product_id' => $product->id,
                'price' => $basePrice * 0.85,
                'quantity_from' => 10,
                'quantity_to' => 49,
                'quantity_price' => $basePrice * 0.80, // 20% desconto para 10-49 unidades
            ]);

            PriceTableItem::create([
                'price_table_id' => $atacado->id,
                'product_id' => $product->id,
                'price' => $basePrice * 0.85,
                'quantity_from' => 50,
                'quantity_to' => null,
                'quantity_price' => $basePrice * 0.75, // 25% desconto para 50+ unidades
            ]);
        }

        $this->command->info('Tabelas de preço criadas com sucesso!');
    }
}

