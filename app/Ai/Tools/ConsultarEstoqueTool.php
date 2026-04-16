<?php

namespace App\Ai\Tools;

use App\Models\Product;
use App\Models\StockMovement;

class ConsultarEstoqueTool extends BaseTool
{
    public function name(): string
    {
        return 'consultar_estoque';
    }

    public function description(): string
    {
        return 'Consulta o saldo de estoque e informações de produtos. Use quando o usuário perguntar sobre quantidade em estoque, preço de custo, preço de venda ou dados de um produto.';
    }

    public function parameters(): array
    {
        return [
            'busca' => [
                'type'        => 'string',
                'description' => 'Nome, código ou EAN do produto para buscar.',
            ],
            'limite' => [
                'type'        => 'integer',
                'description' => 'Número máximo de produtos a retornar (padrão: 5, máximo: 10).',
            ],
        ];
    }

    protected function requiredParams(): array
    {
        return ['busca'];
    }

    public function execute(array $params, int $userId): array
    {
        $limite = min((int) ($params['limite'] ?? 5), 10);
        $busca  = $params['busca'] ?? '';

        $produtos = Product::where(function ($q) use ($busca) {
            $q->where('name', 'like', "%{$busca}%")
              ->orWhere('product_code', 'like', "%{$busca}%")
              ->orWhere('ean', 'like', "%{$busca}%");
        })
        ->withTrashed(false)
        ->limit($limite)
        ->get(['id', 'name', 'product_code', 'ean', 'sale_price', 'cost_price', 'stock_quantity', 'unit_id']);

        if ($produtos->isEmpty()) {
            return ['resultado' => "Nenhum produto encontrado com a busca: '{$busca}'."];
        }

        return [
            'produtos' => $produtos->map(fn ($p) => [
                'codigo'          => $p->product_code ?? 'N/A',
                'nome'            => $p->name,
                'ean'             => $p->ean ?? 'N/A',
                'estoque_atual'   => (float) ($p->stock_quantity ?? 0),
                'preco_custo'     => $this->formatMoney((float) ($p->cost_price ?? 0)),
                'preco_venda'     => $this->formatMoney((float) ($p->sale_price ?? 0)),
            ])->toArray(),
            'total' => $produtos->count(),
        ];
    }
}

