<?php

namespace Database\Seeders;

use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class PurchaseRequisitionSeeder extends Seeder
{
    public function run(): void
    {
        if (PurchaseRequisition::count() > 0) {
            $this->command->warn('Requisições de compra já existem. Seeder ignorado.');
            return;
        }

        $user     = User::where('is_admin', true)->first() ?? User::first();
        $products = Product::take(8)->get();

        if (!$user || $products->isEmpty()) {
            $this->command->warn('Usuário/Produtos não encontrados.');
            return;
        }

        $requisitions = [
            [
                'number'      => 'REQ-2026-001',
                'title'       => 'Reposição de Estoque - Mercearia',
                'status'      => 'aprovada',
                'priority'    => 'alta',
                'requester_id'=> $user->id,
                'department'  => 'Estoque',
                'needed_by'   => '2026-06-01',
                'justification'=> 'Produtos com estoque abaixo do mínimo. Necessário reposição urgente.',
                'approved_by' => $user->id,
                'approved_at' => '2026-05-10 10:00:00',
                'notes'       => 'Prioridade para produtos de alta rotatividade.',
                'created_by'  => $user->id,
                'items' => [
                    ['product_index' => 0, 'qty' => 200, 'unit' => 'UN', 'notes' => 'Produto A'],
                    ['product_index' => 1, 'qty' => 150, 'unit' => 'UN', 'notes' => null],
                ],
            ],
            [
                'number'      => 'REQ-2026-002',
                'title'       => 'Compra de Material de Escritório',
                'status'      => 'rascunho',
                'priority'    => 'normal',
                'requester_id'=> $user->id,
                'department'  => 'Administrativo',
                'needed_by'   => '2026-06-15',
                'justification'=> 'Reposição de materiais de escritório para o trimestre.',
                'notes'       => null,
                'created_by'  => $user->id,
                'items' => [
                    ['product_index' => 2, 'qty' => 10, 'unit' => 'CX', 'notes' => 'Papel A4'],
                    ['product_index' => 3, 'qty' => 5,  'unit' => 'UN', 'notes' => 'Cartuchos de tinta'],
                ],
            ],
            [
                'number'      => 'REQ-2026-003',
                'title'       => 'Aquisição de EPI - Segurança',
                'status'      => 'aguardando_aprovacao',
                'priority'    => 'urgente',
                'requester_id'=> $user->id,
                'department'  => 'Segurança',
                'needed_by'   => '2026-05-30',
                'justification'=> 'EPIs vencidos e em falta para equipe de logística.',
                'notes'       => 'Verificar conformidade com normas NR-6.',
                'created_by'  => $user->id,
                'items' => [
                    ['product_index' => 4, 'qty' => 30, 'unit' => 'UN', 'notes' => 'Capacetes'],
                    ['product_index' => 5, 'qty' => 30, 'unit' => 'PAR', 'notes' => 'Luvas de proteção'],
                    ['product_index' => 6, 'qty' => 20, 'unit' => 'UN', 'notes' => 'Óculos de proteção'],
                ],
            ],
            [
                'number'      => 'REQ-2026-004',
                'title'       => 'Reposição Padaria - Insumos',
                'status'      => 'rejeitada',
                'priority'    => 'normal',
                'requester_id'=> $user->id,
                'department'  => 'Padaria',
                'needed_by'   => '2026-05-25',
                'justification'=> 'Insumos para produção do mês de junho.',
                'rejection_reason' => 'Orçamento excedido. Renegociar com fornecedor e resubmeter.',
                'notes'       => null,
                'created_by'  => $user->id,
                'items' => [
                    ['product_index' => 7, 'qty' => 500, 'unit' => 'KG', 'notes' => 'Farinha de trigo'],
                ],
            ],
        ];

        foreach ($requisitions as $reqData) {
            $items = $reqData['items'];
            unset($reqData['items']);

            $req = PurchaseRequisition::create($reqData);

            foreach ($items as $item) {
                $product = $products->get($item['product_index'] % $products->count());
                PurchaseRequisitionItem::create([
                    'purchase_requisition_id' => $req->id,
                    'product_id'              => $product?->id,
                    'description'             => $product?->name ?? ($item['notes'] ?? 'Item'),
                    'quantity'                => $item['qty'],
                    'unit'                    => $item['unit'],
                    'estimated_price'         => 0,
                    'notes'                   => $item['notes'],
                ]);
            }
        }

        $this->command->info('✅ ' . count($requisitions) . ' requisições de compra criadas.');
    }
}


