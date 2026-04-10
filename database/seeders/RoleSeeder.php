<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // [ code, name, department, description ]
        $roles = [
            // ── Direção / Alta Gestão ─────────────────────────────────
            ['DIR-001', 'Diretor Geral',            'Diretoria',      'Responsável geral pela gestão estratégica do supermercado'],
            ['DIR-002', 'Diretor Comercial',         'Diretoria',      'Responsável pelas estratégias comerciais e de vendas'],
            ['DIR-003', 'Diretor Financeiro',        'Diretoria',      'Responsável pela gestão financeira e contábil'],

            // ── Gerência ─────────────────────────────────────────────
            ['GER-001', 'Gerente de Loja',           'Operações',      'Gerencia todas as operações diárias da loja'],
            ['GER-002', 'Gerente Administrativo',    'Administrativo', 'Gerencia os processos administrativos e de RH'],
            ['GER-003', 'Gerente Financeiro',        'Financeiro',     'Gestão de contas a pagar, receber e fluxo de caixa'],
            ['GER-004', 'Gerente de Compras',        'Compras',        'Responsável pelas negociações com fornecedores e compras'],
            ['GER-005', 'Gerente de Estoque',        'Estoque',        'Controla e gerencia o estoque de produtos'],
            ['GER-006', 'Gerente de TI',             'TI',             'Gerencia os sistemas e infraestrutura de TI'],

            // ── Supervisão ───────────────────────────────────────────
            ['SUP-001', 'Supervisor de Checkout',    'Operações',      'Supervisiona os operadores de caixa e fluxo de clientes'],
            ['SUP-002', 'Supervisor de Estoque',     'Estoque',        'Supervisiona repositores e controle de estoque'],
            ['SUP-003', 'Supervisor de Segurança',   'Segurança',      'Coordena a equipe de vigilância e prevenção de perdas'],
            ['SUP-004', 'Supervisor de Padaria',     'Padaria',        'Supervisiona a produção e qualidade da padaria'],
            ['SUP-005', 'Supervisor de Açougue',     'Açougue',        'Supervisiona os açougueiros e controle de carnes'],

            // ── Administrativo / Financeiro ───────────────────────────
            ['ADM-001', 'Analista Administrativo',   'Administrativo', 'Suporte administrativo geral'],
            ['ADM-002', 'Analista de RH',            'RH',             'Recrutamento, seleção e gestão de pessoal'],
            ['ADM-003', 'Analista Financeiro',       'Financeiro',     'Análise e controle financeiro'],
            ['ADM-004', 'Auxiliar Administrativo',   'Administrativo', 'Suporte às tarefas administrativas'],
            ['ADM-005', 'Analista de TI',            'TI',             'Suporte técnico e manutenção de sistemas'],

            // ── Operacional — Caixas ──────────────────────────────────
            ['CXA-001', 'Operador(a) de Caixa',      'Checkout',       'Operação dos caixas e atendimento ao cliente no checkout'],
            ['CXA-002', 'Operador(a) de SAC',        'Atendimento',    'Atendimento ao cliente, trocas e devoluções'],

            // ── Operacional — Estoque ─────────────────────────────────
            ['EST-001', 'Repositor(a)',               'Estoque',        'Reposição de produtos nas gôndolas e controle de validade'],
            ['EST-002', 'Conferente de Estoque',     'Estoque',        'Conferência de notas fiscais e recebimento de mercadorias'],
            ['EST-003', 'Auxiliar de Estoque',       'Estoque',        'Suporte nas atividades de estoque e movimentação de produtos'],

            // ── Padaria e Confeitaria ─────────────────────────────────
            ['PAD-001', 'Padeiro(a)',                 'Padaria',        'Produção de pães, bolos e produtos de confeitaria'],
            ['PAD-002', 'Confeiteiro(a)',             'Padaria',        'Produção de bolos, tortas e produtos decorados'],
            ['PAD-003', 'Auxiliar de Padaria',       'Padaria',        'Suporte nas atividades de padaria e confeitaria'],

            // ── Açougue ──────────────────────────────────────────────
            ['ACO-001', 'Açougueiro(a)',              'Açougue',        'Corte, embalagem e exposição de carnes'],
            ['ACO-002', 'Auxiliar de Açougue',       'Açougue',        'Suporte nas atividades do açougue'],

            // ── Hortifruti ───────────────────────────────────────────
            ['HOR-001', 'Atendente de Hortifruti',   'Hortifruti',     'Organização e atendimento na seção de hortifruti'],

            // ── Segurança ─────────────────────────────────────────────
            ['SEG-001', 'Vigilante/Segurança',       'Segurança',      'Vigilância patrimonial e prevenção de perdas'],

            // ── Limpeza / Manutenção ──────────────────────────────────
            ['LMP-001', 'Auxiliar de Limpeza',       'Conservação',    'Limpeza e conservação das instalações'],
            ['MNT-001', 'Técnico de Manutenção',     'Manutenção',     'Manutenção preventiva e corretiva de equipamentos'],

            // ── Logística / Entrega ───────────────────────────────────
            ['LOG-001', 'Motorista/Entregador',      'Logística',      'Transporte e entrega de mercadorias'],
            ['LOG-002', 'Auxiliar de Entrega',       'Logística',      'Suporte nas operações de entrega'],

            // ── Outros ────────────────────────────────────────────────
            ['MKT-001', 'Promotor(a) de Vendas',     'Marketing',      'Ações promocionais e demonstração de produtos'],
        ];

        foreach ($roles as [$code, $name, $dept, $desc]) {
            Role::firstOrCreate(
                ['code' => $code],
                [
                    'name'             => $name,
                    'department'       => $dept,
                    'description'      => $desc,
                    'is_active'        => true,
                    'allow_assignment' => true,
                ]
            );
        }

        $this->command->info('✅ ' . count($roles) . ' funções semeadas.');
    }
}

