<?php

namespace Database\Seeders;

use App\Models\CashFlow;
use Illuminate\Database\Seeder;

class CashFlowSeeder extends Seeder
{
    public function run(): void
    {
        if (CashFlow::count() > 0) {
            $this->command->warn('Fluxo de caixa já possui dados. Seeder ignorado.');
            return;
        }

        $entries = [
            ['name' => 'Recebimento Venda à Vista',           'description' => 'Recebimento de vendas no caixa'],
            ['name' => 'Recebimento Boleto',                   'description' => 'Recebimento via boleto bancário'],
            ['name' => 'Recebimento Pix',                      'description' => 'Recebimento via Pix'],
            ['name' => 'Recebimento Cartão de Crédito',        'description' => 'Recebimento via cartão de crédito'],
            ['name' => 'Recebimento Cartão de Débito',         'description' => 'Recebimento via cartão de débito'],
            ['name' => 'Recebimento Transferência Bancária',   'description' => 'TED/DOC recebido'],
            ['name' => 'Pagamento Fornecedores',               'description' => 'Saída para pagamento de fornecedores'],
            ['name' => 'Pagamento Folha de Salários',          'description' => 'Saída para folha de pagamento mensal'],
            ['name' => 'Pagamento Encargos Sociais (FGTS/INSS)','description' => 'Recolhimento de encargos trabalhistas'],
            ['name' => 'Pagamento Aluguel',                    'description' => 'Saída mensal de aluguel do imóvel'],
            ['name' => 'Pagamento Energia Elétrica',           'description' => 'Conta de energia - CEMIG'],
            ['name' => 'Pagamento Água e Esgoto',              'description' => 'Conta de saneamento'],
            ['name' => 'Pagamento Internet e Telefonia',       'description' => 'Conta de telecomunicações'],
            ['name' => 'Pagamento Impostos Federais',          'description' => 'IRPJ, CSLL, PIS, COFINS'],
            ['name' => 'Pagamento Impostos Municipais',        'description' => 'ISS e outros impostos municipais'],
            ['name' => 'Retirada de Sócios (Pró-labore)',      'description' => 'Retirada mensal dos sócios'],
            ['name' => 'Aquisição de Ativo Permanente',        'description' => 'Compra de equipamentos e móveis'],
            ['name' => 'Empréstimo Bancário Recebido',         'description' => 'Entrada de recurso financiado'],
            ['name' => 'Amortização de Empréstimo',            'description' => 'Pagamento de parcela de empréstimo'],
            ['name' => 'Aplicação Financeira',                 'description' => 'Transferência para investimento'],
            ['name' => 'Resgate de Aplicação',                 'description' => 'Resgate de investimento financeiro'],
        ];

        foreach ($entries as $entry) {
            CashFlow::create($entry);
        }

        $this->command->info('✅ ' . count($entries) . ' registros de fluxo de caixa criados.');
    }
}

