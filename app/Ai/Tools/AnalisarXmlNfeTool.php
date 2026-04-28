<?php

namespace App\Ai\Tools;

/**
 * Tool: Analisar XML de NF-e/NFC-e.
 *
 * Extrai e valida os campos críticos de um XML fiscal:
 * - CFOP, NCM, CST, totais, natureza da operação
 * - Tags obrigatórias ausentes
 * - Inconsistências entre campos
 *
 * Permite ao agente fazer diagnóstico real sem depender da SEFAZ.
 */
class AnalisarXmlNfeTool extends BaseTool
{
    public function name(): string
    {
        return 'analisar_xml_nfe';
    }

    public function description(): string
    {
        return 'Analisa o conteúdo de um XML de NF-e, NFC-e ou CT-e enviado pelo usuário. '
            . 'Extrai campos críticos (CFOP, NCM, CST, totais, emitente, destinatário, impostos) e '
            . 'identifica tags ausentes, valores inválidos e inconsistências. '
            . 'Use quando o usuário colar ou mencionar o conteúdo de um XML fiscal.';
    }

    public function parameters(): array
    {
        return [
            'xml' => [
                'type'        => 'string',
                'description' => 'Conteúdo do XML da NF-e/NFC-e/CT-e (pode ser parcial). Obrigatório.',
            ],
        ];
    }

    protected function requiredParams(): array
    {
        return ['xml'];
    }

    public function execute(array $params, int $userId): array
    {
        $xmlRaw = trim($params['xml'] ?? '');

        if (empty($xmlRaw)) {
            return ['erro' => 'XML não informado.'];
        }

        // Suprime erros de parsing para XML malformado
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlRaw);
        libxml_clear_errors();

        if ($xml === false) {
            return [
                'valido'      => false,
                'erro_parser' => 'XML inválido ou malformado. Verifique se o conteúdo foi colado corretamente.',
                'dica'        => 'Cole o XML completo, incluindo as tags raiz <nfeProc> ou <NFe>.',
            ];
        }

        $resultado = [
            'valido'     => true,
            'tipo'       => $this->detectarTipo($xmlRaw),
            'campos'     => [],
            'ausentes'   => [],
            'alertas'    => [],
            'resumo_impostos' => [],
        ];

        // Detecta namespace e normaliza
        $ns = $xml->getNamespaces(true);
        $nfe = $xml;

        // Tenta navegar até infNFe
        $caminhos = ['NFe/infNFe', 'nfeProc/NFe/infNFe', 'infNFe'];
        $infNFe   = null;

        foreach ($caminhos as $caminho) {
            try {
                $tentativa = $xml->xpath('//' . str_replace('/', '/', $caminho));
                if (! empty($tentativa)) {
                    $infNFe = $tentativa[0];
                    break;
                }
            } catch (\Throwable) {}
        }

        // Extração de campos por XPath (tolerante a falhas)
        $campos = [
            'chave_acesso'    => $this->xpathValue($xml, '//@Id'),
            'cnpj_emitente'   => $this->xpathValue($xml, '//emit/CNPJ'),
            'nome_emitente'   => $this->xpathValue($xml, '//emit/xNome'),
            'ie_emitente'     => $this->xpathValue($xml, '//emit/IE'),
            'cnpj_destinatario' => $this->xpathValue($xml, '//dest/CNPJ'),
            'cpf_destinatario'  => $this->xpathValue($xml, '//dest/CPF'),
            'nome_destinatario' => $this->xpathValue($xml, '//dest/xNome'),
            'natureza_operacao' => $this->xpathValue($xml, '//natOp'),
            'data_emissao'      => $this->xpathValue($xml, '//ide/dhEmi'),
            'ambiente'          => $this->xpathValue($xml, '//ide/tpAmb'),
            'tipo_nf'           => $this->xpathValue($xml, '//ide/tpNF'),
            'serie'             => $this->xpathValue($xml, '//ide/serie'),
            'numero'            => $this->xpathValue($xml, '//ide/nNF'),
            'valor_total_nf'    => $this->xpathValue($xml, '//ICMSTot/vNF'),
            'valor_icms'        => $this->xpathValue($xml, '//ICMSTot/vICMS'),
            'valor_ipi'         => $this->xpathValue($xml, '//ICMSTot/vIPI'),
            'valor_pis'         => $this->xpathValue($xml, '//ICMSTot/vPIS'),
            'valor_cofins'      => $this->xpathValue($xml, '//ICMSTot/vCOFINS'),
            'protocolo_sefaz'   => $this->xpathValue($xml, '//protNFe//nProt'),
            'status_sefaz'      => $this->xpathValue($xml, '//protNFe//cStat'),
            'mensagem_sefaz'    => $this->xpathValue($xml, '//protNFe//xMotivo'),
        ];

        // Filtra campos vazios
        $resultado['campos'] = array_filter($campos);

        // Produtos / Itens
        $itens = $xml->xpath('//det');
        $produtos = [];

        foreach ($itens as $i => $det) {
            $cfop = $this->xpathValue($det, './/CFOP');
            $ncm  = $this->xpathValue($det, './/NCM');
            $cst  = $this->xpathValue($det, './/CST') ?: $this->xpathValue($det, './/CSOSN');

            $produto = [
                'item'    => ($i + 1),
                'codigo'  => $this->xpathValue($det, './/cProd'),
                'descricao' => $this->xpathValue($det, './/xProd'),
                'ncm'     => $ncm,
                'cfop'    => $cfop,
                'cst'     => $cst,
                'quantidade' => $this->xpathValue($det, './/qCom'),
                'valor_unitario' => $this->xpathValue($det, './/vUnCom'),
                'valor_total'    => $this->xpathValue($det, './/vProd'),
            ];

            // Alertas por item
            if (empty($ncm)) {
                $resultado['alertas'][] = "Item {$produto['item']} ({$produto['descricao']}): NCM ausente.";
            } elseif (strlen(preg_replace('/\D/', '', $ncm)) !== 8) {
                $resultado['alertas'][] = "Item {$produto['item']} ({$produto['descricao']}): NCM '{$ncm}' não tem 8 dígitos.";
            }

            if (empty($cfop)) {
                $resultado['alertas'][] = "Item {$produto['item']} ({$produto['descricao']}): CFOP ausente.";
            }

            if (empty($cst)) {
                $resultado['alertas'][] = "Item {$produto['item']} ({$produto['descricao']}): CST/CSOSN ausente.";
            }

            $produtos[] = array_filter($produto);
        }

        $resultado['itens']        = $produtos;
        $resultado['total_itens']  = count($produtos);

        // Campos obrigatórios ausentes (nível NF)
        $obrigatorios = [
            'cnpj_emitente'    => 'CNPJ do Emitente',
            'nome_emitente'    => 'Nome do Emitente',
            'ie_emitente'      => 'IE do Emitente',
            'natureza_operacao' => 'Natureza da Operação',
            'valor_total_nf'   => 'Valor Total da NF',
        ];

        foreach ($obrigatorios as $campo => $label) {
            if (empty($resultado['campos'][$campo])) {
                $resultado['ausentes'][] = $label;
            }
        }

        // Destinatário: CNPJ ou CPF obrigatório
        if (empty($resultado['campos']['cnpj_destinatario']) && empty($resultado['campos']['cpf_destinatario'])) {
            $resultado['ausentes'][] = 'CPF/CNPJ do Destinatário';
        }

        // Ambiente legível
        if (isset($resultado['campos']['ambiente'])) {
            $resultado['campos']['ambiente'] = $resultado['campos']['ambiente'] === '1' ? 'Produção' : 'Homologação';
        }

        // Tipo NF legível
        if (isset($resultado['campos']['tipo_nf'])) {
            $resultado['campos']['tipo_nf'] = $resultado['campos']['tipo_nf'] === '1' ? 'Saída' : 'Entrada';
        }

        // Resumo diagnóstico
        $resultado['diagnostico_geral'] = empty($resultado['alertas']) && empty($resultado['ausentes'])
            ? '✅ XML válido — nenhum problema estrutural encontrado.'
            : '⚠️ XML com problemas: ' . count($resultado['alertas']) . ' alerta(s), ' . count($resultado['ausentes']) . ' campo(s) obrigatório(s) ausente(s).';

        return $resultado;
    }

    private function detectarTipo(string $xml): string
    {
        if (str_contains($xml, 'cteProc') || str_contains($xml, '<CTe')) {
            return 'CT-e';
        }

        if (str_contains($xml, 'mod>65') || str_contains($xml, '<mod>65')) {
            return 'NFC-e';
        }

        return 'NF-e';
    }

    private function xpathValue($xml, string $xpath): string
    {
        try {
            $result = $xml->xpath($xpath);

            if (! empty($result)) {
                return trim((string) $result[0]);
            }
        } catch (\Throwable) {}

        return '';
    }
}

