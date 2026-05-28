<?php

namespace Database\Seeders;

use App\Models\Cotacao;
use App\Models\CotacaoItem;
use App\Models\CotacaoResposta;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class CotacaoSeeder extends Seeder
{
    public function run(): void
    {
        if (Cotacao::count() > 0) {
            $this->command->warn('Cotações já existem. Seeder ignorado.');
            return;
        }

        $user      = User::where('is_admin', true)->first() ?? User::first();
        $products  = Product::take(6)->get();
        $suppliers = Supplier::take(3)->get();

        if ($products->isEmpty() || $suppliers->isEmpty()) {
            $this->command->warn('Produtos/Fornecedores não encontrados. Execute os seeders anteriores primeiro.');
            return;
        }

        $cotacoes = [
            [
                'number'       => 'COT-2026-001',
                'title'        => 'Cotação - Insumos de Padaria',
                'status'       => 'aprovada',
                'deadline_date'=> '2026-05-01',
                'notes'        => 'Solicitar prazo mínimo de 30 dias para pagamento.',
                'created_by'   => $user?->id,
                'items' => [
                    ['product_index' => 0, 'qty' => 500, 'unit' => 'KG'],
                    ['product_index' => 1, 'qty' => 200, 'unit' => 'UN'],
                ],
            ],
            [
                'number'       => 'COT-2026-002',
                'title'        => 'Cotação - Produtos de Limpeza',
                'status'       => 'aberta',
                'deadline_date'=> '2026-06-10',
                'notes'        => 'Preferência para fornecedores com entrega inclusa.',
                'created_by'   => $user?->id,
                'items' => [
                    ['product_index' => 2, 'qty' => 100, 'unit' => 'UN'],
                    ['product_index' => 3, 'qty' => 50,  'unit' => 'CX'],
                ],
            ],
            [
                'number'       => 'COT-2026-003',
                'title'        => 'Cotação - Equipamentos de TI',
                'status'       => 'rascunho',
                'deadline_date'=> '2026-06-20',
                'notes'        => 'Avaliar garantia e suporte técnico.',
                'created_by'   => $user?->id,
                'items' => [
                    ['product_index' => 4, 'qty' => 5,  'unit' => 'UN'],
                    ['product_index' => 5, 'qty' => 10, 'unit' => 'UN'],
                ],
            ],
        ];

        foreach ($cotacoes as $cotData) {
            $itemsData = $cotData['items'];
            unset($cotData['items']);

            $cotacao = Cotacao::create($cotData);

            $createdItems = [];
            foreach ($itemsData as $itemData) {
                $product = $products->get($itemData['product_index'] % $products->count());
                $item    = CotacaoItem::create([
                    'cotacao_id'  => $cotacao->id,
                    'product_id'  => $product?->id,
                    'description' => $product?->name ?? 'Item',
                    'sku'         => $product?->sku ?? null,
                    'unit'        => $itemData['unit'],
                    'quantity'    => $itemData['qty'],
                ]);
                $createdItems[] = $item;
            }

            // Adicionar respostas para cotações encerradas/abertas
            if ($cotacao->status !== 'rascunho') {
                foreach ($createdItems as $item) {
                    foreach ($suppliers->take(2) as $idx => $supplier) {
                        $basePrice = rand(10, 200) / 10 * 10;
                        CotacaoResposta::create([
                            'cotacao_id'      => $cotacao->id,
                            'cotacao_item_id' => $item->id,
                            'supplier_id'     => $supplier->id,
                            'unit_price'      => $basePrice + ($idx * $basePrice * 0.05),
                            'delivery_days'   => rand(5, 30),
                            'notes'           => $idx === 0 ? 'Melhor preço garantido.' : null,
                            'selected'        => $cotacao->status === 'aprovada' && $idx === 0,
                        ]);
                    }
                }
            }
        }

        $this->command->info('✅ ' . count($cotacoes) . ' cotações criadas com itens e respostas.');
    }
}

