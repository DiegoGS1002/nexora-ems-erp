<?php

namespace Database\Seeders;

use App\Models\BaccaratAccount;
use Illuminate\Database\Seeder;

class BankAccountSeeder extends Seeder
{
    public function run(): void
    {
        if (BaccaratAccount::count() > 0) {
            $this->command->warn('Contas bancárias já existem. Seeder ignorado.');
            return;
        }

        $accounts = [
            [
                'name'              => 'Conta Corrente Principal',
                'bank_name'         => 'Banco do Brasil',
                'agency'            => '1234-5',
                'number'            => '00012345-6',
                'type'              => 'corrente',
                'balance'           => 85000.00,
                'predicted_balance' => 90000.00,
                'color'             => '#1E40AF',
                'is_active'         => true,
                'is_reconciled'     => true,
                'last_reconciled_at'=> '2026-05-01',
                'description'       => 'Conta corrente principal para operações do dia a dia.',
            ],
            [
                'name'              => 'Conta Poupança Reserva',
                'bank_name'         => 'Caixa Econômica Federal',
                'agency'            => '0987',
                'number'            => '00099887-1',
                'type'              => 'poupanca',
                'balance'           => 150000.00,
                'predicted_balance' => 150000.00,
                'color'             => '#065F46',
                'is_active'         => true,
                'is_reconciled'     => false,
                'description'       => 'Poupança para reserva de capital de giro.',
            ],
            [
                'name'              => 'Conta Itaú - Pagamentos',
                'bank_name'         => 'Itaú Unibanco',
                'agency'            => '4321',
                'number'            => '00044321-8',
                'type'              => 'corrente',
                'balance'           => 42000.00,
                'predicted_balance' => 38000.00,
                'color'             => '#EA580C',
                'is_active'         => true,
                'is_reconciled'     => false,
                'description'       => 'Conta para pagamento de fornecedores.',
            ],
            [
                'name'              => 'Caixa Interno Loja',
                'bank_name'         => null,
                'agency'            => null,
                'number'            => null,
                'type'              => 'caixa_interno',
                'balance'           => 5000.00,
                'predicted_balance' => 5000.00,
                'color'             => '#7C3AED',
                'is_active'         => true,
                'is_reconciled'     => true,
                'last_reconciled_at'=> '2026-05-27',
                'description'       => 'Caixa físico da loja para pequenas despesas.',
            ],
            [
                'name'              => 'Conta Digital Nubank',
                'bank_name'         => 'Nu Pagamentos S.A.',
                'agency'            => '0001',
                'number'            => '88887777-9',
                'type'              => 'digital',
                'balance'           => 12500.00,
                'predicted_balance' => 12500.00,
                'color'             => '#6D28D9',
                'is_active'         => true,
                'is_reconciled'     => false,
                'description'       => 'Conta digital para recebimentos via Pix.',
            ],
        ];

        foreach ($accounts as $account) {
            BaccaratAccount::create($account);
        }

        $this->command->info('✅ ' . count($accounts) . ' contas bancárias criadas.');
    }
}

