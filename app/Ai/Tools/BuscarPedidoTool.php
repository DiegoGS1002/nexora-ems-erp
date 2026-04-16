<?php

namespace App\Ai\Tools;

use App\Models\SalesOrder;

class BuscarPedidoTool extends BaseTool
{
    public function name(): string
    {
        return 'buscar_pedido';
    }

    public function description(): string
    {
        return 'Busca informações sobre pedidos de venda. Use quando o usuário perguntar sobre o status, valor, itens ou data de entrega de um pedido. Pode buscar pelo número do pedido ou listar os pedidos recentes.';
    }

    public function parameters(): array
    {
        return [
            'numero_pedido' => [
                'type'        => 'string',
                'description' => 'Número do pedido (order_number). Opcional — se não informado, retorna os últimos pedidos.',
            ],
            'limite' => [
                'type'        => 'integer',
                'description' => 'Número máximo de pedidos a retornar (padrão: 5, máximo: 10).',
            ],
        ];
    }

    public function execute(array $params, int $userId): array
    {
        $limite = min((int) ($params['limite'] ?? 5), 10);

        $query = SalesOrder::with(['client:id,name', 'items:sales_order_id,quantity,unit_price'])
            ->where('user_id', $userId)
            ->latest('order_date');

        if (! empty($params['numero_pedido'])) {
            $query->where('order_number', 'like', '%' . $params['numero_pedido'] . '%');
        }

        $pedidos = $query->limit($limite)->get();

        if ($pedidos->isEmpty()) {
            return ['resultado' => 'Nenhum pedido encontrado para os critérios informados.'];
        }

        $dados = $pedidos->map(fn ($p) => [
            'numero'         => $p->order_number,
            'cliente'        => $p->client?->name ?? 'N/A',
            'status'         => $p->status?->label() ?? $p->status,
            'data_pedido'    => $this->formatDate($p->order_date),
            'entrega_prev'   => $this->formatDate($p->expected_delivery_date),
            'valor_total'    => $this->formatMoney((float) ($p->total_amount ?? 0)),
            'itens'          => $p->items->count(),
        ])->toArray();

        return ['pedidos' => $dados, 'total' => $pedidos->count()];
    }
}

