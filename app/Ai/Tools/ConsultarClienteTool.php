<?php

namespace App\Ai\Tools;

use App\Models\Client;

class ConsultarClienteTool extends BaseTool
{
    public function name(): string
    {
        return 'consultar_cliente';
    }

    public function description(): string
    {
        return 'Consulta informações de clientes cadastrados no sistema. Use quando o usuário perguntar sobre dados de um cliente específico (nome, CNPJ, telefone, email) ou quiser listar clientes.';
    }

    public function parameters(): array
    {
        return [
            'busca' => [
                'type'        => 'string',
                'description' => 'Nome, CPF ou CNPJ do cliente para buscar.',
            ],
            'limite' => [
                'type'        => 'integer',
                'description' => 'Número máximo de clientes a retornar (padrão: 5, máximo: 10).',
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

        $clientes = Client::where(function ($q) use ($busca) {
            $q->where('name', 'like', "%{$busca}%")
              ->orWhere('social_name', 'like', "%{$busca}%")
              ->orWhere('taxNumber', 'like', "%{$busca}%");
        })
        ->limit($limite)
        ->get(['id', 'name', 'social_name', 'taxNumber', 'email', 'phone', 'tipo_pessoa']);

        if ($clientes->isEmpty()) {
            return ['resultado' => "Nenhum cliente encontrado com a busca: '{$busca}'."];
        }

        return [
            'clientes' => $clientes->map(fn ($c) => [
                'nome'        => $c->name,
                'razao_social'=> $c->social_name ?? 'N/A',
                'documento'   => $c->taxNumber ?? 'N/A',
                'email'       => $c->email ?? 'N/A',
                'telefone'    => $c->phone ?? 'N/A',
                'tipo'        => $c->tipo_pessoa?->value ?? 'N/A',
            ])->toArray(),
            'total' => $clientes->count(),
        ];
    }
}

