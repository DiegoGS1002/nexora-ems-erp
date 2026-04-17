<?php

namespace App\Ai\Tools;

use App\Models\FiscalNote;
use App\Enums\FiscalNoteStatus;

class VerificarNfeTool extends BaseTool
{
    public function name(): string
    {
        return 'verificar_nfe';
    }

    public function description(): string
    {
        return 'Consulta o status e os detalhes de Notas Fiscais Eletrônicas (NF-e) no sistema. '
            . 'Use quando o usuário relatar problemas com notas fiscais, erros de emissão, rejeições da SEFAZ ou quiser saber o status de uma nota. '
            . 'Retorna o status atual, mensagem da SEFAZ, dados do cliente e informações de diagnóstico. '
            . 'Pode buscar pela chave de acesso, número da nota ou listar as últimas notas do usuário.';
    }

    public function parameters(): array
    {
        return [
            'numero_nota' => [
                'type'        => 'string',
                'description' => 'Número da nota fiscal (invoice_number). Opcional.',
            ],
            'chave_acesso' => [
                'type'        => 'string',
                'description' => 'Chave de acesso de 44 dígitos da NF-e. Opcional.',
            ],
            'status' => [
                'type'        => 'string',
                'description' => 'Filtrar por status: draft, sent, authorized, rejected, cancelled, denied. Opcional.',
                'enum'        => ['draft', 'sent', 'authorized', 'rejected', 'cancelled', 'denied'],
            ],
            'limite' => [
                'type'        => 'integer',
                'description' => 'Número máximo de notas a retornar (padrão: 5, máximo: 10).',
            ],
        ];
    }

    public function execute(array $params, int $userId): array
    {
        $limite = min((int) ($params['limite'] ?? 5), 10);

        $query = FiscalNote::with('client:id,name,social_name,taxNumber,email,phone')
            ->where('emitted_by', $userId)
            ->latest();

        if (!empty($params['numero_nota'])) {
            $query->where('invoice_number', 'like', '%' . $params['numero_nota'] . '%');
        }

        if (!empty($params['chave_acesso'])) {
            $query->where('access_key', 'like', '%' . $params['chave_acesso'] . '%');
        }

        if (!empty($params['status'])) {
            $query->where('status', $params['status']);
        }

        $notas = $query->limit($limite)->get();

        if ($notas->isEmpty()) {
            return [
                'resultado'  => 'Nenhuma nota fiscal encontrada para os critérios informados.',
                'diagnostico' => 'Verifique se o número da nota está correto ou se o usuário emitiu alguma nota.',
            ];
        }

        $dados = $notas->map(function ($n) {
            $statusLabels = [
                'draft'      => 'Rascunho',
                'sent'       => 'Enviada à SEFAZ',
                'authorized' => 'Autorizada ✅',
                'rejected'   => 'Rejeitada ❌',
                'cancelled'  => 'Cancelada',
                'denied'     => 'Denegada ⛔',
            ];

            $statusVal = $n->status instanceof FiscalNoteStatus ? $n->status->value : (string) $n->status;

            $item = [
                'numero'            => $n->invoice_number,
                'serie'             => $n->series,
                'tipo'              => strtoupper($n->type ?? 'nfe'),
                'ambiente'          => $n->environment === 'production' ? 'Produção' : 'Homologação',
                'status'            => $statusLabels[$statusVal] ?? $statusVal,
                'status_raw'        => $statusVal,
                'valor_total'       => $this->formatMoney((float) ($n->amount ?? 0)),
                'cliente'           => $n->client?->social_name ?? $n->client?->name ?? $n->client_name ?? 'N/A',
                'documento_cliente' => $n->client?->taxNumber ?? 'N/A',
                'data_emissao'      => $this->formatDate($n->created_at),
                'data_autorizacao'  => $this->formatDate($n->authorized_at),
                'protocolo'         => $n->protocol ?? 'N/A',
                'chave_acesso'      => $n->access_key ?? 'N/A',
            ];

            // Mensagem SEFAZ — crítica para diagnóstico
            if ($n->sefaz_message) {
                $item['mensagem_sefaz'] = $n->sefaz_message;
                $item['diagnostico']    = $this->diagnosticarErroCefaz($n->sefaz_message, $n->client);
            }

            if ($n->cancel_reason) {
                $item['motivo_cancelamento'] = $n->cancel_reason;
            }

            // Campos faltantes no cliente
            if ($n->client) {
                $faltantes = [];
                if (empty($n->client->taxNumber)) $faltantes[] = 'CPF/CNPJ';
                if (empty($n->client->email))     $faltantes[] = 'E-mail';
                if (empty($n->client->phone))     $faltantes[] = 'Telefone';

                if (!empty($faltantes)) {
                    $item['campos_faltantes_cliente'] = $faltantes;
                }
            }

            return $item;
        })->toArray();

        return [
            'notas'  => $dados,
            'total'  => $notas->count(),
            'resumo' => $this->gerarResumo($notas),
        ];
    }

    private function diagnosticarErroCefaz(string $mensagem, $cliente): string
    {
        $msg = strtolower($mensagem);

        // Mapeamento de rejeições SEFAZ → diagnóstico acionável
        $diagnosticos = [
            '203'  => 'Rejeição 203: Emissor não habilitado para emissão no ambiente de produção. Verifique o certificado digital e as configurações da empresa.',
            '204'  => 'Rejeição 204: Duplicidade de NF-e — uma nota com este número/série já foi autorizada. Use um número diferente.',
            '206'  => 'Rejeição 206: NF-e autorizada. Emissão em contingência não permitida após autorização.',
            '214'  => 'Rejeição 214: O CNPJ do emitente é inválido ou não está habilitado na SEFAZ.',
            '238'  => 'Rejeição 238: Número máximo de numeração atingido para a série. Troque a série da nota.',
            '539'  => 'Rejeição 539: CNPJ do destinatário inválido ou não cadastrado na Receita Federal.',
            '702'  => 'Rejeição 702: Código NCM inválido ou inexistente. Verifique o NCM do(s) produto(s) em Cadastro > Produtos.',
            '741'  => 'Rejeição 741: CFOP inválido para a operação. Revise o tipo de operação fiscal selecionado.',
            'ncm'  => 'O campo NCM do produto está inválido ou desatualizado. Acesse Cadastro > Produtos e corrija o NCM.',
            'cfop' => 'O CFOP informado não é compatível com esta operação. Verifique em Fiscal > Tipos de Operação.',
            'certificado' => 'Problema com o certificado digital. Verifique em Fiscal > Configurações se o certificado está válido e não vencido.',
            'ie inválida' => 'A Inscrição Estadual do destinatário está inválida. Corrija em Cadastro > Clientes.',
            'ie do emitente' => 'A Inscrição Estadual do emitente está inválida ou ausente. Verifique em Fiscal > Configurações.',
        ];

        foreach ($diagnosticos as $chave => $diagnostico) {
            if (str_contains($msg, $chave)) {
                return $diagnostico;
            }
        }

        return 'Erro retornado pela SEFAZ: "' . $mensagem . '". Consulte a documentação da SEFAZ ou entre em contato com o suporte técnico.';
    }

    private function gerarResumo($notas): array
    {
        $resumo = ['total' => $notas->count()];

        $porStatus = $notas->groupBy(fn ($n) => $n->status instanceof FiscalNoteStatus ? $n->status->value : (string) $n->status);
        foreach ($porStatus as $status => $grupo) {
            $resumo[$status] = $grupo->count();
        }

        return $resumo;
    }
}

