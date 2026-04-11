<?php

namespace App\Services;

class PricingService
{
    /**
     * Calcula o preço final de um produto usando markup divisor
     */
    public function calculateFinalPrice(array $params): array
    {
        $custoMateriaPrima = (float) ($params['custo_materia_prima'] ?? 0);
        $despesas          = (float) ($params['despesas'] ?? 0);
        $imposto           = (float) ($params['imposto'] ?? 0);
        $comissao          = (float) ($params['comissao'] ?? 0);
        $frete             = (float) ($params['frete'] ?? 0);
        $prazo             = (float) ($params['prazo'] ?? 0);
        $vpc               = (float) ($params['vpc'] ?? 0);
        $assistencia       = (float) ($params['assistencia'] ?? 0);
        $inadimplencia     = (float) ($params['inadimplencia'] ?? 0);
        $lucro             = (float) ($params['lucro'] ?? 0);

        // 1. Custo do Produto (custo + despesas sobre custo)
        $custoProduto = $custoMateriaPrima + ($custoMateriaPrima * ($despesas / 100));

        // 2. Soma dos percentuais sobre venda
        $somaPercentuais = ($imposto + $comissao + $frete + $prazo + $vpc + $assistencia + $inadimplencia + $lucro) / 100;

        // 3. Validação: não permitir soma >= 100%
        if ($somaPercentuais >= 1) {
            return [
                'error' => 'A soma dos percentuais não pode ser igual ou maior que 100%',
                'soma_percentuais' => $somaPercentuais * 100,
            ];
        }

        // 4. Índice de Preço (markup divisor)
        $indicePreco = 1 / (1 - $somaPercentuais);

        // 5. Preço Final
        $precoFinal = $custoProduto * $indicePreco;

        // 6. Distribuição dos valores sobre o preço final
        $valorImposto       = ($imposto / 100) * $precoFinal;
        $valorComissao      = ($comissao / 100) * $precoFinal;
        $valorFrete         = ($frete / 100) * $precoFinal;
        $valorPrazo         = ($prazo / 100) * $precoFinal;
        $valorVpc           = ($vpc / 100) * $precoFinal;
        $valorAssistencia   = ($assistencia / 100) * $precoFinal;
        $valorInadimplencia = ($inadimplencia / 100) * $precoFinal;
        $valorLucro         = ($lucro / 100) * $precoFinal;

        // 7. Comercialização (soma de todos os custos sobre venda)
        $comercializacao = $valorImposto + $valorComissao + $valorFrete + $valorPrazo +
                          $valorVpc + $valorAssistencia + $valorInadimplencia;

        // 8. Preço mínimo (sem lucro)
        $percentuaisSemLucro = ($imposto + $comissao + $frete + $prazo + $vpc + $assistencia + $inadimplencia) / 100;
        $precoMinimo = $percentuaisSemLucro < 1 ? $custoProduto / (1 - $percentuaisSemLucro) : 0;

        // 9. Margem real
        $margemReal = $precoFinal > 0 ? (($precoFinal - $custoProduto) / $precoFinal) * 100 : 0;

        return [
            'custo_materia_prima'   => round($custoMateriaPrima, 2),
            'valor_despesas'        => round($custoMateriaPrima * ($despesas / 100), 2),
            'custo_produto'         => round($custoProduto, 2),

            'soma_percentuais'      => round($somaPercentuais * 100, 2),
            'indice_preco'          => round($indicePreco, 4),
            'preco_final'           => round($precoFinal, 2),

            'valor_imposto'         => round($valorImposto, 2),
            'valor_comissao'        => round($valorComissao, 2),
            'valor_frete'           => round($valorFrete, 2),
            'valor_prazo'           => round($valorPrazo, 2),
            'valor_vpc'             => round($valorVpc, 2),
            'valor_assistencia'     => round($valorAssistencia, 2),
            'valor_inadimplencia'   => round($valorInadimplencia, 2),
            'valor_lucro'           => round($valorLucro, 2),

            'comercializacao'       => round($comercializacao, 2),
            'preco_minimo'          => round($precoMinimo, 2),
            'margem_real'           => round($margemReal, 2),
        ];
    }

    /**
     * Calcula múltiplas tabelas de preço para um produto
     */
    public function calculateMultiplePriceTables(float $custoMateriaPrima, array $tables): array
    {
        $results = [];

        foreach ($tables as $tableName => $params) {
            $params['custo_materia_prima'] = $custoMateriaPrima;
            $results[$tableName] = $this->calculateFinalPrice($params);
        }

        return $results;
    }
}

