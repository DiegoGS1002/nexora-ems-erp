<?php

namespace App\Ai\Tools;

use App\Models\SefazDiagnostic;

/**
 * Tool: Consultar diagnóstico SEFAZ estruturado.
 *
 * Etapa 1 da hierarquia de busca:
 *   SQL Estruturado (SefazDiagnostics) → RAG → LLM
 *
 * Para rejeições com código exato, retorna diagnóstico determinístico
 * sem precisar de embeddings ou LLM.
 */
class ConsultarDiagnosticoSefazTool extends BaseTool
{
    public function name(): string
    {
        return 'consultar_diagnostico_sefaz';
    }

    public function description(): string
    {
        return 'Consulta o banco de diagnósticos estruturado da SEFAZ pelo código de rejeição. '
            . 'Use SEMPRE que o usuário mencionar um código de rejeição numérico (ex: Rejeição 702, Rej 778, código 938). '
            . 'Retorna causa, solução passo a passo e módulo do Nexora ERP para correção. '
            . 'Esta ferramenta é mais precisa que qualquer outra fonte para rejeições SEFAZ.';
    }

    public function parameters(): array
    {
        return [
            'codigo' => [
                'type'        => 'string',
                'description' => 'Código numérico da rejeição SEFAZ (ex: "702", "778", "938"). Obrigatório.',
            ],
        ];
    }

    protected function requiredParams(): array
    {
        return ['codigo'];
    }

    public function execute(array $params, int $userId): array
    {
        $codigo = trim($params['codigo'] ?? '');

        if (empty($codigo)) {
            return ['erro' => 'Informe o código de rejeição SEFAZ.'];
        }

        // Normaliza: remove "Rejeição", "Rej.", pontuação
        $codigo = preg_replace('/[^0-9]/', '', $codigo);

        $diag = SefazDiagnostic::findByCode($codigo);

        if (! $diag) {
            return [
                'encontrado' => false,
                'codigo'     => $codigo,
                'mensagem'   => "Rejeição {$codigo} não encontrada no banco de diagnósticos local. "
                    . 'Consulte o MOC (Manual de Orientação do Contribuinte) da SEFAZ ou use a base RAG para diagnóstico.',
                'sugestao'   => 'Solicite ao usuário a mensagem completa retornada pelo sistema para análise manual.',
            ];
        }

        return [
            'encontrado'      => true,
            'codigo'          => $diag->rejection_code,
            'titulo'          => $diag->titulo,
            'severidade'      => $diag->severity,
            'modulo_correcao' => $diag->module,
            'causa'           => $diag->causa,
            'solucao'         => $diag->solucao,
            'codigos_relacionados' => $diag->related_codes ?? [],
            'tags'            => $diag->tags ?? [],
            'instrucao_agente' => 'Use EXATAMENTE as informações de causa e solução acima na sua resposta. '
                . 'Cite o módulo do Nexora ERP para correção. Não invente informações adicionais.',
        ];
    }
}

