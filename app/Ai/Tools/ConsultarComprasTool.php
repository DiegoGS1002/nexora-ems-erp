<?php

namespace App\Ai\Tools;

use App\Models\PurchaseOrder;
use App\Models\PurchaseRequisition;
use App\Models\Supplier;
use App\Models\Cotacao;

class ConsultarComprasTool extends BaseTool
{
    public function name(): string
    {
        return 'consultar_compras';
    }

    public function description(): string
    {
        return 'Consulta pedidos de compra, cotações, requisições e fornecedores. '
            . 'Use quando o usuário perguntar sobre pedido de compra, fornecedor, cotação, requisição ou entrada de mercadoria via compra.';
    }

    public function parameters(): array
    {
        return [
            'tipo' => [
                'type'        => 'string',
                'enum'        => ['pedidos', 'requisicoes', 'cotacoes', 'fornecedores'],
                'description' => 'Tipo de consulta.',
            ],
            'numero_pedido' => [
                'type'        => 'string',
                'description' => 'Número do pedido de compra para busca direta.',
            ],
            'fornecedor_nome' => [
                'type'        => 'string',
                'description' => 'Nome ou parte do nome do fornecedor.',
            ],
            'status' => [
                'type'        => 'string',
                'description' => 'Status do pedido (ex: pendente, aprovado, recebido, cancelado).',
            ],
            'limite' => [
                'type'        => 'integer',
                'description' => 'Número máximo de registros (padrão: 8, máximo: 15).',
            ],
        ];
    }

    protected function requiredParams(): array
    {
        return ['tipo'];
    }

    public function execute(array $params, int $userId): array
    {
        $tipo   = $params['tipo'] ?? 'pedidos';
        $limite = min((int) ($params['limite'] ?? 8), 15);

        return match ($tipo) {
            'pedidos'      => $this->consultarPedidos($params, $limite),
            'requisicoes'  => $this->consultarRequisicoes($params, $limite),
            'cotacoes'     => $this->consultarCotacoes($params, $limite),
            'fornecedores' => $this->consultarFornecedores($params, $limite),
            default        => ['erro' => 'Tipo inválido. Use: pedidos, requisicoes, cotacoes, fornecedores.'],
        };
    }

    private function consultarPedidos(array $params, int $limite): array
    {
        $query = PurchaseOrder::query()->latest()->limit($limite);

        if (! empty($params['numero_pedido'])) {
            $query->where('id', $params['numero_pedido'])
                  ->orWhere('number', $params['numero_pedido']);
        }

        if (! empty($params['status'])) {
            $query->where('status', $params['status']);
        }

        if (! empty($params['fornecedor_nome'])) {
            $query->whereHas('supplier', fn ($q) => $q->where('name', 'like', '%' . $params['fornecedor_nome'] . '%'));
        }

        $pedidos = $query->with('supplier:id,name')->get();

        if ($pedidos->isEmpty()) {
            return ['resultado' => 'Nenhum pedido de compra encontrado.'];
        }

        return [
            'total'   => $pedidos->count(),
            'pedidos' => $pedidos->map(fn ($p) => [
                'id'          => $p->id,
                'numero'      => $p->number ?? $p->id,
                'fornecedor'  => $p->supplier?->name ?? 'N/A',
                'status'      => $p->status,
                'total'       => $this->formatMoney((float) ($p->total ?? 0)),
                'criado_em'   => $this->formatDate($p->created_at),
                'previsao'    => $this->formatDate($p->expected_date ?? null),
            ])->toArray(),
        ];
    }

    private function consultarRequisicoes(array $params, int $limite): array
    {
        $requisicoes = PurchaseRequisition::query()->latest()->limit($limite)->get();

        if ($requisicoes->isEmpty()) {
            return ['resultado' => 'Nenhuma requisição de compra encontrada.'];
        }

        return [
            'total'        => $requisicoes->count(),
            'requisicoes'  => $requisicoes->map(fn ($r) => [
                'id'       => $r->id,
                'descricao'=> $r->description ?? 'N/A',
                'status'   => $r->status ?? 'Pendente',
                'criado_em'=> $this->formatDate($r->created_at),
            ])->toArray(),
        ];
    }

    private function consultarCotacoes(array $params, int $limite): array
    {
        $cotacoes = Cotacao::query()->latest()->limit($limite)->get();

        if ($cotacoes->isEmpty()) {
            return ['resultado' => 'Nenhuma cotação encontrada.'];
        }

        return [
            'total'    => $cotacoes->count(),
            'cotacoes' => $cotacoes->map(fn ($c) => [
                'id'        => $c->id,
                'titulo'    => $c->title ?? $c->description ?? 'N/A',
                'status'    => $c->status ?? 'Aberta',
                'criado_em' => $this->formatDate($c->created_at),
            ])->toArray(),
        ];
    }

    private function consultarFornecedores(array $params, int $limite): array
    {
        $query = Supplier::query()->latest()->limit($limite);

        if (! empty($params['fornecedor_nome'])) {
            $query->where('name', 'like', '%' . $params['fornecedor_nome'] . '%');
        }

        $fornecedores = $query->get(['id', 'name', 'cnpj', 'email', 'phone', 'city', 'state']);

        if ($fornecedores->isEmpty()) {
            return ['resultado' => 'Nenhum fornecedor encontrado.'];
        }

        return [
            'total'        => $fornecedores->count(),
            'fornecedores' => $fornecedores->map(fn ($f) => [
                'id'     => $f->id,
                'nome'   => $f->name,
                'cnpj'   => $f->cnpj,
                'email'  => $f->email,
                'fone'   => $f->phone,
                'cidade' => $f->city,
                'uf'     => $f->state,
            ])->toArray(),
        ];
    }
}

