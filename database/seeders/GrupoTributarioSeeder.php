<?php

namespace Database\Seeders;

use App\Models\GrupoTributario;
use App\Models\TipoOperacaoFiscal;
use Illuminate\Database\Seeder;

class GrupoTributarioSeeder extends Seeder
{
    public function run(): void
    {
        // Buscar tipos de operação já criados
        $vendaEst = TipoOperacaoFiscal::where('codigo', 'VENDA-EST')->first();
        $compraEst = TipoOperacaoFiscal::where('codigo', 'COMPRA-EST')->first();
        $vendaSN = TipoOperacaoFiscal::where('codigo', 'VENDA-SN-EST')->first();

        $grupos = [
            // Mercadoria para Revenda – Simples Nacional
            [
                'codigo'                   => 'GT-MERC-SN',
                'nome'                     => 'Mercadoria para Revenda – Simples Nacional',
                'descricao'                => 'Grupo tributário padrão para empresas optantes pelo Simples Nacional que revendem mercadorias.',
                'regime_tributario'        => 'simples_nacional',
                'ncm'                      => null,
                'tipo_operacao_saida_id'   => $vendaSN?->id,
                'tipo_operacao_entrada_id' => $compraEst?->id,
                'icms_cst'                 => '400',   // CSOSN 400 – Não Tributada pelo SN
                'icms_aliquota'            => 0.00,
                'pis_cst'                  => '07',    // Isento
                'pis_aliquota'             => 0.00,
                'cofins_cst'               => '07',
                'cofins_aliquota'          => 0.00,
                'is_active'                => true,
            ],

            // Mercadoria para Revenda – Lucro Presumido
            [
                'codigo'                   => 'GT-MERC-LP',
                'nome'                     => 'Mercadoria para Revenda – Lucro Presumido',
                'descricao'                => 'Grupo tributário para empresas no regime de Lucro Presumido.',
                'regime_tributario'        => 'lucro_presumido',
                'ncm'                      => null,
                'tipo_operacao_saida_id'   => $vendaEst?->id,
                'tipo_operacao_entrada_id' => $compraEst?->id,
                'icms_cst'                 => '00',    // Tributada Integralmente
                'icms_modalidade_bc'       => 3,       // Valor da Operação
                'icms_aliquota'            => 12.00,
                'pis_cst'                  => '01',    // Tributável
                'pis_aliquota'             => 0.65,
                'cofins_cst'               => '01',
                'cofins_aliquota'          => 3.00,
                'is_active'                => true,
            ],

            // Produto com Substituição Tributária
            [
                'codigo'                   => 'GT-ST',
                'nome'                     => 'Mercadoria com Substituição Tributária',
                'descricao'                => 'Para produtos sujeitos à ST (ex: bebidas, combustíveis, autopeças).',
                'regime_tributario'        => 'todos',
                'ncm'                      => null,
                'tipo_operacao_saida_id'   => TipoOperacaoFiscal::where('codigo', 'VENDA-ST-EST')->first()?->id,
                'tipo_operacao_entrada_id' => $compraEst?->id,
                'icms_cst'                 => '60',    // ICMS Cobrado por ST
                'icms_aliquota'            => 0.00,
                'pis_cst'                  => '01',
                'pis_aliquota'             => 0.65,
                'cofins_cst'               => '01',
                'cofins_aliquota'          => 3.00,
                'is_active'                => true,
            ],

            // Serviços
            [
                'codigo'                   => 'GT-SERVICO',
                'nome'                     => 'Prestação de Serviços',
                'descricao'                => 'Grupo para serviços gerais (não sujeitos a IPI).',
                'regime_tributario'        => 'todos',
                'ncm'                      => null,
                'tipo_operacao_saida_id'   => $vendaEst?->id,
                'tipo_operacao_entrada_id' => null,
                'icms_cst'                 => '41',    // Não Tributada
                'icms_aliquota'            => 0.00,
                'ipi_cst'                  => '53',    // Saída Não Tributada
                'ipi_aliquota'             => 0.00,
                'pis_cst'                  => '01',
                'pis_aliquota'             => 0.65,
                'cofins_cst'               => '01',
                'cofins_aliquota'          => 3.00,
                'is_active'                => true,
            ],

            // Produto Isento
            [
                'codigo'                   => 'GT-ISENTO',
                'nome'                     => 'Mercadoria Isenta',
                'descricao'                => 'Para produtos com isenção fiscal (ex: livros, alguns alimentos).',
                'regime_tributario'        => 'todos',
                'ncm'                      => null,
                'tipo_operacao_saida_id'   => $vendaEst?->id,
                'tipo_operacao_entrada_id' => $compraEst?->id,
                'icms_cst'                 => '40',    // Isenta
                'icms_aliquota'            => 0.00,
                'pis_cst'                  => '07',
                'pis_aliquota'             => 0.00,
                'cofins_cst'               => '07',
                'cofins_aliquota'          => 0.00,
                'is_active'                => true,
            ],
        ];

        foreach ($grupos as $g) {
            GrupoTributario::firstOrCreate(
                ['codigo' => $g['codigo']],
                $g
            );
        }
    }
}

