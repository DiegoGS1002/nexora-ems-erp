<?php

namespace App\Ai\Tools;

use App\Models\ProductionOrder;
use App\Models\ProductionOrderProduct;
use App\Models\Stock;

class ConsultarProducaoTool extends BaseTool
{
    public function name(): string
    {
        return 'consultar_producao';
    }

    public function description(): string
    {
        return 'Consulta ordens de produção, status, insumos e fichas técnicas. '
            . 'Use quando o usuário perguntar sobre ordem de produção, OP, manufatura, ficha técnica, insumos ou produto acabado.';
    }

    public function parameters(): array
    {
        return [
            'numero_op' => [
                'type'        => 'string',
                'description' => 'Número da Ordem de Produção (OP) para busca direta.',
            ],
            'status' => [
                'type'        => 'string',
                'description' => 'Filtrar por status: aberta, em_andamento, finalizada, cancelada.',
            ],
            'produto_nome' => [
                'type'        => 'string',
                'description' => 'Nome do produto acabado para filtrar.',
            ],
            'limite' => [
                'type'        => 'integer',
                'description' => 'Número máximo de registros (padrão: 8, máximo: 15).',
            ],
        ];
    }

    public function execute(array $params, int $userId): array
    {
        $limite = min((int) ($params['limite'] ?? 8), 15);
        $query  = ProductionOrder::query()->latest()->limit($limite);

        if (! empty($params['numero_op'])) {
            $query->where('id', $params['numero_op'])
                  ->orWhere('number', $params['numero_op']);
        }

        if (! empty($params['status'])) {
            $query->where('status', $params['status']);
        }

        if (! empty($params['produto_nome'])) {
            $query->whereHas('product', fn ($q) => $q->where('name', 'like', '%' . $params['produto_nome'] . '%'));
        }

        $ordens = $query->with(['product:id,name', 'items'])->get();

        if ($ordens->isEmpty()) {
            return ['resultado' => 'Nenhuma ordem de produção encontrada para os critérios informados.'];
        }

        return [
            'total'  => $ordens->count(),
            'ordens' => $ordens->map(fn ($op) => [
                'id'              => $op->id,
                'numero'          => $op->number ?? $op->id,
                'produto'         => $op->product?->name ?? ($op->product_name ?? 'N/A'),
                'quantidade'      => $op->quantity ?? 0,
                'status'          => $op->status,
                'data_abertura'   => $this->formatDate($op->created_at),
                'data_previsao'   => $this->formatDate($op->expected_date ?? null),
                'data_conclusao'  => $this->formatDate($op->completed_at ?? null),
                'insumos'         => ($op->items ?? collect())->map(fn ($item) => [
                    'produto'     => $item->product_name ?? $item->product?->name ?? 'N/A',
                    'quantidade'  => $item->quantity,
                    'unidade'     => $item->unit ?? 'UN',
                ])->toArray(),
            ])->toArray(),
        ];
    }
}

