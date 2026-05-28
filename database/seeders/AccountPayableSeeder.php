<?php

namespace Database\Seeders;

use App\Models\AccountPayable;
use App\Models\Supplier;
use App\Models\PlanOfAccount;
use Illuminate\Database\Seeder;

class AccountPayableSeeder extends Seeder
{
    public function run(): void
    {
        if (AccountPayable::count() > 0) {
            $this->command->warn('Contas a pagar já existem. Seeder ignorado.');
            return;
        }

        $supplierId = Supplier::first()?->id;
        $chartId    = PlanOfAccount::where('code', 'like', '3%')->first()?->id;

        $bills = [
            [
                'description_title' => 'Aluguel - Junho/2026',
                'supplier_id'       => $supplierId,
                'chart_of_account_id'=> $chartId,
                'amount'            => 12000.00,
                'due_date_at'       => '2026-06-05',
                'status'            => 'pending',
                'observation'       => 'Aluguel do imóvel comercial - contrato anual.',
            ],
            [
                'description_title' => 'Energia Elétrica - Maio/2026',
                'supplier_id'       => null,
                'chart_of_account_id'=> $chartId,
                'amount'            => 3850.00,
                'due_date_at'       => '2026-05-30',
                'payment_date'      => '2026-05-28',
                'paid_amount'       => 3850.00,
                'status'            => 'paid',
                'observation'       => 'Conta de energia CEMIG.',
            ],
            [
                'description_title' => 'Fornecedor Alpha - NF 001234',
                'supplier_id'       => $supplierId,
                'chart_of_account_id'=> $chartId,
                'amount'            => 28500.00,
                'due_date_at'       => '2026-06-10',
                'status'            => 'pending',
            ],
            [
                'description_title' => 'Internet e Telefonia - Junho/2026',
                'supplier_id'       => null,
                'chart_of_account_id'=> $chartId,
                'amount'            => 890.00,
                'due_date_at'       => '2026-06-15',
                'status'            => 'pending',
                'is_recurring'      => true,
                'recurrence_day'    => 15,
            ],
            [
                'description_title' => 'Seguro Patrimonial - Parcela 3/12',
                'supplier_id'       => null,
                'chart_of_account_id'=> $chartId,
                'amount'            => 1200.00,
                'due_date_at'       => '2026-06-20',
                'status'            => 'pending',
                'is_recurring'      => true,
                'recurrence_day'    => 20,
            ],
            [
                'description_title' => 'Manutenção de Equipamentos',
                'supplier_id'       => $supplierId,
                'chart_of_account_id'=> $chartId,
                'amount'            => 4500.00,
                'due_date_at'       => '2026-05-15',
                'payment_date'      => '2026-05-14',
                'paid_amount'       => 4500.00,
                'status'            => 'paid',
            ],
            [
                'description_title' => 'Material de Limpeza - Mai/2026',
                'supplier_id'       => $supplierId,
                'chart_of_account_id'=> $chartId,
                'amount'            => 750.00,
                'due_date_at'       => '2026-05-20',
                'status'            => 'overdue',
            ],
            [
                'description_title' => 'Sistema ERP - Licença Mensal',
                'supplier_id'       => null,
                'chart_of_account_id'=> $chartId,
                'amount'            => 1800.00,
                'due_date_at'       => '2026-06-01',
                'status'            => 'pending',
                'is_recurring'      => true,
                'recurrence_day'    => 1,
            ],
        ];

        foreach ($bills as $bill) {
            AccountPayable::create($bill);
        }

        $this->command->info('✅ ' . count($bills) . ' contas a pagar criadas.');
    }
}

