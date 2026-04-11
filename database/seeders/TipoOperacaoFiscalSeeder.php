<?php

namespace Database\Seeders;

use App\Models\TipoOperacaoFiscal;
use Illuminate\Database\Seeder;

class TipoOperacaoFiscalSeeder extends Seeder
{
    public function run(): void
    {
        $operacoes = [
            // ── Saídas Estaduais ─────────────────────────────────
            [
                'codigo'            => 'VENDA-EST',
                'descricao'         => 'Venda de Mercadoria – Estadual',
                'natureza_operacao' => 'Venda de Mercadoria',
                'tipo_movimento'    => 'saida',
                'cfop'              => '5102',
                'icms_cst'          => '00',
                'icms_modalidade_bc'=> 3,
                'icms_aliquota'     => 12.00,
                'pis_cst'           => '01',
                'pis_aliquota'      => 0.65,
                'cofins_cst'        => '01',
                'cofins_aliquota'   => 3.00,
            ],
            // ── Saídas Interestaduais ─────────────────────────────
            [
                'codigo'            => 'VENDA-INT',
                'descricao'         => 'Venda de Mercadoria – Interestadual',
                'natureza_operacao' => 'Venda de Mercadoria',
                'tipo_movimento'    => 'saida',
                'cfop'              => '6102',
                'icms_cst'          => '00',
                'icms_modalidade_bc'=> 3,
                'icms_aliquota'     => 7.00,
                'pis_cst'           => '01',
                'pis_aliquota'      => 0.65,
                'cofins_cst'        => '01',
                'cofins_aliquota'   => 3.00,
            ],
            // ── Saída com Substituição Tributária (Estadual) ──────
            [
                'codigo'            => 'VENDA-ST-EST',
                'descricao'         => 'Venda de Mercadoria com ST – Estadual',
                'natureza_operacao' => 'Venda de Mercadoria c/ ST',
                'tipo_movimento'    => 'saida',
                'cfop'              => '5405',
                'icms_cst'          => '60',
                'icms_aliquota'     => 0.00,
                'pis_cst'           => '01',
                'pis_aliquota'      => 0.65,
                'cofins_cst'        => '01',
                'cofins_aliquota'   => 3.00,
            ],
            // ── Saída com Substituição Tributária (Interestadual) ─
            [
                'codigo'            => 'VENDA-ST-INT',
                'descricao'         => 'Venda de Mercadoria com ST – Interestadual',
                'natureza_operacao' => 'Venda de Mercadoria c/ ST',
                'tipo_movimento'    => 'saida',
                'cfop'              => '6404',
                'icms_cst'          => '10',
                'icms_modalidade_bc'=> 3,
                'icms_aliquota'     => 12.00,
                'pis_cst'           => '01',
                'pis_aliquota'      => 0.65,
                'cofins_cst'        => '01',
                'cofins_aliquota'   => 3.00,
            ],
            // ── Devolução de Compra (Estadual) ────────────────────
            [
                'codigo'            => 'DEV-COMPRA-EST',
                'descricao'         => 'Devolução de Compra – Estadual',
                'natureza_operacao' => 'Devolução de Compra',
                'tipo_movimento'    => 'saida',
                'cfop'              => '5202',
                'icms_cst'          => '00',
                'icms_modalidade_bc'=> 3,
                'icms_aliquota'     => 12.00,
                'pis_cst'           => '01',
                'pis_aliquota'      => 0.65,
                'cofins_cst'        => '01',
                'cofins_aliquota'   => 3.00,
            ],
            // ── Entradas ──────────────────────────────────────────
            [
                'codigo'            => 'COMPRA-EST',
                'descricao'         => 'Compra de Mercadoria – Estadual',
                'natureza_operacao' => 'Compra de Mercadoria',
                'tipo_movimento'    => 'entrada',
                'cfop'              => '1102',
                'icms_cst'          => '00',
                'icms_modalidade_bc'=> 3,
                'icms_aliquota'     => 12.00,
                'pis_cst'           => '50',
                'pis_aliquota'      => 0.65,
                'cofins_cst'        => '50',
                'cofins_aliquota'   => 3.00,
            ],
            [
                'codigo'            => 'COMPRA-INT',
                'descricao'         => 'Compra de Mercadoria – Interestadual',
                'natureza_operacao' => 'Compra de Mercadoria',
                'tipo_movimento'    => 'entrada',
                'cfop'              => '2102',
                'icms_cst'          => '00',
                'icms_modalidade_bc'=> 3,
                'icms_aliquota'     => 12.00,
                'pis_cst'           => '50',
                'pis_aliquota'      => 0.65,
                'cofins_cst'        => '50',
                'cofins_aliquota'   => 3.00,
            ],
            // ── Devolução de Venda (Entrada Estadual) ─────────────
            [
                'codigo'            => 'DEV-VENDA-EST',
                'descricao'         => 'Devolução de Venda – Estadual',
                'natureza_operacao' => 'Devolução de Venda',
                'tipo_movimento'    => 'entrada',
                'cfop'              => '1202',
                'icms_cst'          => '00',
                'icms_modalidade_bc'=> 3,
                'icms_aliquota'     => 12.00,
                'pis_cst'           => '70',
                'pis_aliquota'      => 0.00,
                'cofins_cst'        => '70',
                'cofins_aliquota'   => 0.00,
            ],
            // ── Simples Nacional ──────────────────────────────────
            [
                'codigo'            => 'VENDA-SN-EST',
                'descricao'         => 'Venda de Mercadoria – Simples Nacional Estadual',
                'natureza_operacao' => 'Venda de Mercadoria',
                'tipo_movimento'    => 'saida',
                'cfop'              => '5102',
                'icms_cst'          => '400',
                'icms_aliquota'     => 0.00,
                'pis_cst'           => '07',
                'pis_aliquota'      => 0.00,
                'cofins_cst'        => '07',
                'cofins_aliquota'   => 0.00,
                'observacoes'       => 'Para empresas optantes pelo Simples Nacional.',
            ],
            // ── Remessa ───────────────────────────────────────────
            [
                'codigo'            => 'REMESSA',
                'descricao'         => 'Remessa para Industrialização / Beneficiamento',
                'natureza_operacao' => 'Remessa p/ Industrialização',
                'tipo_movimento'    => 'saida',
                'cfop'              => '5949',
                'icms_cst'          => '41',
                'icms_aliquota'     => 0.00,
                'pis_cst'           => '08',
                'pis_aliquota'      => 0.00,
                'cofins_cst'        => '08',
                'cofins_aliquota'   => 0.00,
            ],
        ];

        foreach ($operacoes as $op) {
            TipoOperacaoFiscal::firstOrCreate(
                ['codigo' => $op['codigo']],
                array_merge($op, ['is_active' => true])
            );
        }
    }
}

