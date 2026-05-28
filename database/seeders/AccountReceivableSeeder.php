<?php

namespace Database\Seeders;

use App\Models\AccountReceivable;
use App\Models\Client;
use App\Models\PlanOfAccount;
use Illuminate\Database\Seeder;

class AccountReceivableSeeder extends Seeder
{
    public function run(): void
    {
        if (AccountReceivable::count() > 0) {
            $this->command->warn('Contas a receber já existem. Seeder ignorado.');
            return;
        }

        $clientId = Client::first()?->id;
        $chartId  = PlanOfAccount::where('code', 'like', '1%')->first()?->id;

        $receivables = [
            [
                'description_title'  => 'Venda a Prazo - Cliente Mercado Bom Preço - Parcela 1/3',
                'client_id'          => $clientId,
                'chart_of_account_id'=> $chartId,
                'amount'             => 15000.00,
                'received_amount'    => 15000.00,
                'due_date_at'        => '2026-05-10',
                'received_at'        => '2026-05-09',
                'payment_method'     => 'transferencia',
                'installment_number' => 1,
                'status'             => 'received',
            ],
            [
                'description_title'  => 'Venda a Prazo - Cliente Mercado Bom Preço - Parcela 2/3',
                'client_id'          => $clientId,
                'chart_of_account_id'=> $chartId,
                'amount'             => 15000.00,
                'received_amount'    => 0,
                'due_date_at'        => '2026-06-10',
                'payment_method'     => null,
                'installment_number' => 2,
                'status'             => 'pending',
            ],
            [
                'description_title'  => 'Venda a Prazo - Cliente Mercado Bom Preço - Parcela 3/3',
                'client_id'          => $clientId,
                'chart_of_account_id'=> $chartId,
                'amount'             => 15000.00,
                'received_amount'    => 0,
                'due_date_at'        => '2026-07-10',
                'payment_method'     => null,
                'installment_number' => 3,
                'status'             => 'pending',
            ],
            [
                'description_title'  => 'Serviço de Consultoria - Mai/2026',
                'client_id'          => null,
                'chart_of_account_id'=> $chartId,
                'amount'             => 5000.00,
                'received_amount'    => 5000.00,
                'due_date_at'        => '2026-05-20',
                'received_at'        => '2026-05-20',
                'payment_method'     => 'pix',
                'installment_number' => 1,
                'status'             => 'received',
            ],
            [
                'description_title'  => 'Nota Fiscal 0042 - Atacadão Norte',
                'client_id'          => $clientId,
                'chart_of_account_id'=> $chartId,
                'amount'             => 32000.00,
                'received_amount'    => 0,
                'due_date_at'        => '2026-06-05',
                'payment_method'     => null,
                'installment_number' => 1,
                'status'             => 'pending',
            ],
            [
                'description_title'  => 'Boleto Vencido - Cliente Delta',
                'client_id'          => null,
                'chart_of_account_id'=> $chartId,
                'amount'             => 7800.00,
                'received_amount'    => 0,
                'due_date_at'        => '2026-05-05',
                'payment_method'     => 'boleto',
                'installment_number' => 1,
                'status'             => 'overdue',
                'observation'        => 'Cliente informou dificuldades. Aguardando negociação.',
            ],
        ];

        foreach ($receivables as $rec) {
            AccountReceivable::create($rec);
        }

        $this->command->info('✅ ' . count($receivables) . ' contas a receber criadas.');
    }
}

