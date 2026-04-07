<?php

namespace Database\Seeders;

use App\Models\PlanOfAccount;
use Illuminate\Database\Seeder;

class PlanoContasSeeder extends Seeder
{
    public function run(): void
    {
        // Evita duplicação
        if (PlanOfAccount::whereNull('parent_id')->exists()) {
            $this->command->warn('Plano de Contas já possui dados. Seeder ignorado.');
            return;
        }

        $structure = [
            [
                'code' => '1',
                'name' => 'Receitas',
                'type' => 'receita',
                'is_selectable' => false,
                'children' => [
                    [
                        'code' => '1.1',
                        'name' => 'Receitas Operacionais',
                        'type' => 'receita',
                        'is_selectable' => false,
                        'children' => [
                            ['code' => '1.1.001', 'name' => 'Venda de Produtos',   'type' => 'receita', 'is_selectable' => true],
                            ['code' => '1.1.002', 'name' => 'Prestação de Serviços', 'type' => 'receita', 'is_selectable' => true],
                            ['code' => '1.1.003', 'name' => 'Comissões Recebidas',  'type' => 'receita', 'is_selectable' => true],
                        ],
                    ],
                    [
                        'code' => '1.2',
                        'name' => 'Receitas Financeiras',
                        'type' => 'receita',
                        'is_selectable' => false,
                        'children' => [
                            ['code' => '1.2.001', 'name' => 'Juros Recebidos',    'type' => 'receita', 'is_selectable' => true],
                            ['code' => '1.2.002', 'name' => 'Rendimentos Financeiros', 'type' => 'receita', 'is_selectable' => true],
                        ],
                    ],
                    [
                        'code' => '1.3',
                        'name' => 'Outras Receitas',
                        'type' => 'receita',
                        'is_selectable' => false,
                        'children' => [
                            ['code' => '1.3.001', 'name' => 'Receitas Diversas',        'type' => 'receita', 'is_selectable' => true],
                            ['code' => '1.3.002', 'name' => 'Recuperação de Despesas',  'type' => 'receita', 'is_selectable' => true],
                        ],
                    ],
                ],
            ],
            [
                'code' => '2',
                'name' => 'Custos',
                'type' => 'despesa',
                'is_selectable' => false,
                'children' => [
                    [
                        'code' => '2.1',
                        'name' => 'Custo de Mercadorias Vendidas',
                        'type' => 'despesa',
                        'is_selectable' => false,
                        'children' => [
                            ['code' => '2.1.001', 'name' => 'CMV — Mercadorias',    'type' => 'despesa', 'is_selectable' => true],
                            ['code' => '2.1.002', 'name' => 'CMV — Matéria-Prima',  'type' => 'despesa', 'is_selectable' => true],
                        ],
                    ],
                    [
                        'code' => '2.2',
                        'name' => 'Custo dos Serviços Prestados',
                        'type' => 'despesa',
                        'is_selectable' => false,
                        'children' => [
                            ['code' => '2.2.001', 'name' => 'Mão de Obra Direta',    'type' => 'despesa', 'is_selectable' => true],
                            ['code' => '2.2.002', 'name' => 'Materiais de Produção', 'type' => 'despesa', 'is_selectable' => true],
                        ],
                    ],
                ],
            ],
            [
                'code' => '3',
                'name' => 'Despesas Operacionais',
                'type' => 'despesa',
                'is_selectable' => false,
                'children' => [
                    [
                        'code' => '3.1',
                        'name' => 'Despesas Administrativas',
                        'type' => 'despesa',
                        'is_selectable' => false,
                        'children' => [
                            ['code' => '3.1.001', 'name' => 'Aluguel',             'type' => 'despesa', 'is_selectable' => true],
                            ['code' => '3.1.002', 'name' => 'Energia Elétrica',    'type' => 'despesa', 'is_selectable' => true],
                            ['code' => '3.1.003', 'name' => 'Água e Saneamento',   'type' => 'despesa', 'is_selectable' => true],
                            ['code' => '3.1.004', 'name' => 'Telefone / Internet', 'type' => 'despesa', 'is_selectable' => true],
                            ['code' => '3.1.005', 'name' => 'Material de Escritório', 'type' => 'despesa', 'is_selectable' => true],
                            ['code' => '3.1.006', 'name' => 'Manutenção e Reparos', 'type' => 'despesa', 'is_selectable' => true],
                        ],
                    ],
                    [
                        'code' => '3.2',
                        'name' => 'Despesas com Pessoal',
                        'type' => 'despesa',
                        'is_selectable' => false,
                        'children' => [
                            ['code' => '3.2.001', 'name' => 'Salários e Ordenados', 'type' => 'despesa', 'is_selectable' => true],
                            ['code' => '3.2.002', 'name' => 'Encargos Sociais (FGTS / INSS)', 'type' => 'despesa', 'is_selectable' => true],
                            ['code' => '3.2.003', 'name' => 'Vale-Transporte',      'type' => 'despesa', 'is_selectable' => true],
                            ['code' => '3.2.004', 'name' => 'Vale-Refeição',        'type' => 'despesa', 'is_selectable' => true],
                            ['code' => '3.2.005', 'name' => 'Plano de Saúde',       'type' => 'despesa', 'is_selectable' => true],
                            ['code' => '3.2.006', 'name' => '13º Salário',          'type' => 'despesa', 'is_selectable' => true],
                            ['code' => '3.2.007', 'name' => 'Férias',               'type' => 'despesa', 'is_selectable' => true],
                        ],
                    ],
                    [
                        'code' => '3.3',
                        'name' => 'Despesas Financeiras',
                        'type' => 'despesa',
                        'is_selectable' => false,
                        'children' => [
                            ['code' => '3.3.001', 'name' => 'Juros e Multas',    'type' => 'despesa', 'is_selectable' => true],
                            ['code' => '3.3.002', 'name' => 'Tarifas Bancárias', 'type' => 'despesa', 'is_selectable' => true],
                            ['code' => '3.3.003', 'name' => 'IOF',               'type' => 'despesa', 'is_selectable' => true],
                        ],
                    ],
                    [
                        'code' => '3.4',
                        'name' => 'Despesas com Veículos e Frota',
                        'type' => 'despesa',
                        'is_selectable' => false,
                        'children' => [
                            ['code' => '3.4.001', 'name' => 'Combustível',           'type' => 'despesa', 'is_selectable' => true],
                            ['code' => '3.4.002', 'name' => 'Manutenção de Veículos','type' => 'despesa', 'is_selectable' => true],
                            ['code' => '3.4.003', 'name' => 'Seguro de Frota',       'type' => 'despesa', 'is_selectable' => true],
                            ['code' => '3.4.004', 'name' => 'IPVA e Licenciamento',  'type' => 'despesa', 'is_selectable' => true],
                        ],
                    ],
                ],
            ],
            [
                'code' => '4',
                'name' => 'Impostos e Tributos',
                'type' => 'despesa',
                'is_selectable' => false,
                'children' => [
                    [
                        'code' => '4.1',
                        'name' => 'Impostos sobre Faturamento',
                        'type' => 'despesa',
                        'is_selectable' => false,
                        'children' => [
                            ['code' => '4.1.001', 'name' => 'ICMS',   'type' => 'despesa', 'is_selectable' => true],
                            ['code' => '4.1.002', 'name' => 'PIS',    'type' => 'despesa', 'is_selectable' => true],
                            ['code' => '4.1.003', 'name' => 'COFINS', 'type' => 'despesa', 'is_selectable' => true],
                            ['code' => '4.1.004', 'name' => 'ISS',    'type' => 'despesa', 'is_selectable' => true],
                            ['code' => '4.1.005', 'name' => 'IPI',    'type' => 'despesa', 'is_selectable' => true],
                        ],
                    ],
                    [
                        'code' => '4.2',
                        'name' => 'Impostos sobre o Lucro',
                        'type' => 'despesa',
                        'is_selectable' => false,
                        'children' => [
                            ['code' => '4.2.001', 'name' => 'IRPJ',  'type' => 'despesa', 'is_selectable' => true],
                            ['code' => '4.2.002', 'name' => 'CSLL',  'type' => 'despesa', 'is_selectable' => true],
                        ],
                    ],
                ],
            ],
            [
                'code' => '5',
                'name' => 'Ativo',
                'type' => 'ativo',
                'is_selectable' => false,
                'children' => [
                    [
                        'code' => '5.1',
                        'name' => 'Ativo Circulante',
                        'type' => 'ativo',
                        'is_selectable' => false,
                        'children' => [
                            ['code' => '5.1.001', 'name' => 'Caixa',                'type' => 'ativo', 'is_selectable' => true],
                            ['code' => '5.1.002', 'name' => 'Bancos',               'type' => 'ativo', 'is_selectable' => true],
                            ['code' => '5.1.003', 'name' => 'Contas a Receber',     'type' => 'ativo', 'is_selectable' => true],
                            ['code' => '5.1.004', 'name' => 'Estoque',              'type' => 'ativo', 'is_selectable' => true],
                        ],
                    ],
                    [
                        'code' => '5.2',
                        'name' => 'Ativo Não Circulante',
                        'type' => 'ativo',
                        'is_selectable' => false,
                        'children' => [
                            ['code' => '5.2.001', 'name' => 'Imóveis',    'type' => 'ativo', 'is_selectable' => true],
                            ['code' => '5.2.002', 'name' => 'Veículos',   'type' => 'ativo', 'is_selectable' => true],
                            ['code' => '5.2.003', 'name' => 'Maquinário', 'type' => 'ativo', 'is_selectable' => true],
                        ],
                    ],
                ],
            ],
            [
                'code' => '6',
                'name' => 'Passivo',
                'type' => 'passivo',
                'is_selectable' => false,
                'children' => [
                    [
                        'code' => '6.1',
                        'name' => 'Passivo Circulante',
                        'type' => 'passivo',
                        'is_selectable' => false,
                        'children' => [
                            ['code' => '6.1.001', 'name' => 'Contas a Pagar',      'type' => 'passivo', 'is_selectable' => true],
                            ['code' => '6.1.002', 'name' => 'Salários a Pagar',    'type' => 'passivo', 'is_selectable' => true],
                            ['code' => '6.1.003', 'name' => 'Impostos a Recolher', 'type' => 'passivo', 'is_selectable' => true],
                        ],
                    ],
                    [
                        'code' => '6.2',
                        'name' => 'Passivo Não Circulante',
                        'type' => 'passivo',
                        'is_selectable' => false,
                        'children' => [
                            ['code' => '6.2.001', 'name' => 'Empréstimos e Financiamentos', 'type' => 'passivo', 'is_selectable' => true],
                            ['code' => '6.2.002', 'name' => 'Debêntures',                   'type' => 'passivo', 'is_selectable' => true],
                        ],
                    ],
                ],
            ],
        ];

        $this->insertTree($structure, null);

        $this->command->info('✅ Plano de Contas semeado com sucesso!');
    }

    protected function insertTree(array $items, ?int $parentId): void
    {
        foreach ($items as $item) {
            $children = $item['children'] ?? [];
            unset($item['children']);

            $account = PlanOfAccount::create(array_merge($item, [
                'parent_id' => $parentId,
                'is_active' => true,
            ]));

            if (!empty($children)) {
                $this->insertTree($children, $account->id);
            }
        }
    }
}

