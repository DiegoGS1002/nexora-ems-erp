<?php

namespace App\Ai\Tools;

use App\Models\TicketSuporte;

class ConsultarTicketTool extends BaseTool
{
    public function name(): string
    {
        return 'consultar_tickets';
    }

    public function description(): string
    {
        return 'Consulta o histórico de tickets de suporte do usuário. Use para verificar tickets anteriores, status de atendimentos ou histórico de problemas reportados.';
    }

    public function parameters(): array
    {
        return [
            'status' => [
                'type'        => 'string',
                'enum'        => ['aberto', 'em_andamento', 'resolvido', 'fechado', 'todos'],
                'description' => 'Filtrar tickets por status.',
            ],
            'limite' => [
                'type'        => 'integer',
                'description' => 'Número máximo de tickets a retornar (padrão: 5).',
            ],
        ];
    }

    public function execute(array $params, int $userId): array
    {
        $limite = min((int) ($params['limite'] ?? 5), 10);
        $status = $params['status'] ?? 'todos';

        $query = TicketSuporte::where('user_id', $userId)->latest();

        if ($status !== 'todos') {
            $query->where('status', $status);
        }

        $tickets = $query->limit($limite)->get(['id', 'assunto', 'status', 'prioridade', 'categoria', 'created_at', 'fechado_em']);

        if ($tickets->isEmpty()) {
            return ['resultado' => 'Nenhum ticket encontrado para os critérios informados.'];
        }

        return [
            'tickets' => $tickets->map(fn ($t) => [
                'assunto'    => $t->assunto,
                'status'     => $t->status?->label() ?? $t->status,
                'prioridade' => $t->prioridade?->label() ?? $t->prioridade,
                'categoria'  => $t->categoria ?? 'N/A',
                'aberto_em'  => $this->formatDate($t->created_at),
                'fechado_em' => $t->fechado_em ? $this->formatDate($t->fechado_em) : 'Em aberto',
            ])->toArray(),
            'total' => $tickets->count(),
        ];
    }
}

