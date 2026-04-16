<?php

namespace App\Ai\Tools;

use App\Models\AccountPayable;
use App\Models\AccountReceivable;
use Illuminate\Support\Carbon;

class ConsultarFinanceiroTool extends BaseTool
{
    public function name(): string
    {
        return 'consultar_financeiro';
    }

    public function description(): string
    {
        return 'Consulta informações financeiras: contas a pagar e a receber. Use quando o usuário perguntar sobre boletos vencidos, pagamentos pendentes, contas a receber ou fluxo de caixa.';
    }

    public function parameters(): array
    {
        return [
            'tipo' => [
                'type'        => 'string',
                'enum'        => ['pagar', 'receber', 'ambos'],
                'description' => 'Tipo de consulta: pagar (contas a pagar), receber (contas a receber) ou ambos.',
            ],
            'status' => [
                'type'        => 'string',
                'enum'        => ['pendente', 'vencido', 'pago', 'todos'],
                'description' => 'Filtrar por status. "vencido" mostra apenas contas em atraso.',
            ],
            'limite' => [
                'type'        => 'integer',
                'description' => 'Número máximo de registros a retornar (padrão: 5, máximo: 10).',
            ],
        ];
    }

    protected function requiredParams(): array
    {
        return ['tipo'];
    }

    public function execute(array $params, int $userId): array
    {
        $tipo   = $params['tipo'] ?? 'ambos';
        $status = $params['status'] ?? 'pendente';
        $limite = min((int) ($params['limite'] ?? 5), 10);
        $hoje   = Carbon::today();
        $result = [];

        if (in_array($tipo, ['pagar', 'ambos'])) {
            $query = AccountPayable::query();

            match ($status) {
                'pendente' => $query->whereNull('payment_date')->where('due_date_at', '>=', $hoje),
                'vencido'  => $query->whereNull('payment_date')->where('due_date_at', '<', $hoje),
                'pago'     => $query->whereNotNull('payment_date'),
                default    => null,
            };

            $contas = $query->latest('due_date_at')->limit($limite)->get(['description_title', 'amount', 'due_date_at', 'payment_date']);
            $result['contas_pagar'] = [
                'registros' => $contas->map(fn ($c) => [
                    'descricao'    => $c->description_title,
                    'valor'        => $this->formatMoney((float) ($c->amount ?? 0)),
                    'vencimento'   => $this->formatDate($c->due_date_at),
                    'pago_em'      => $c->payment_date ? $this->formatDate($c->payment_date) : 'Pendente',
                ])->toArray(),
                'total' => $contas->count(),
            ];
        }

        if (in_array($tipo, ['receber', 'ambos'])) {
            $result['contas_receber'] = ['registros' => [], 'total' => 0, 'info' => 'Consulte o módulo Financeiro para detalhes.'];
        }

        return $result ?: ['resultado' => 'Nenhum dado financeiro encontrado para os critérios informados.'];
    }
}

