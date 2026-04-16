<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetectAiModule
{
    /**
     * Handle an incoming request and detect the AI module context
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Map route prefixes to AI modules
        $moduleMap = [
            'financeiro' => 'financeiro',
            'contas-pagar' => 'financeiro',
            'contas-receber' => 'financeiro',
            'plano-contas' => 'financeiro',

            'rh' => 'rh',
            'funcionarios' => 'rh',
            'folha' => 'rh',
            'ponto' => 'rh',
            'jornada' => 'rh',
            'holerite' => 'rh',

            'producao' => 'producao',
            'ordem-producao' => 'producao',
            'op' => 'producao',

            'estoque' => 'estoque',
            'movimentacao' => 'estoque',

            'compras' => 'compras',
            'pedido-compra' => 'compras',
            'cotacao' => 'compras',

            'vendas' => 'vendas',
            'pedidos' => 'vendas',

            'logistica' => 'logistica',
            'rotas' => 'logistica',
            'veiculos' => 'logistica',
            'agendamento' => 'logistica',

            'fiscal' => 'fiscal',
            'nfe' => 'fiscal',
            'nf-entrada' => 'fiscal',
            'nf-saida' => 'fiscal',

            'administracao' => 'administracao',
            'usuarios' => 'administracao',
            'roles' => 'administracao',
            'empresas' => 'administracao',

            'cadastro' => 'cadastro',
            'produtos' => 'cadastro',
            'clientes' => 'cadastro',
            'fornecedores' => 'cadastro',

            'suporte' => 'suporte',
        ];

        // Get first segment of URL path
        $segment = $request->segment(1);

        // Detect module
        $module = $moduleMap[$segment] ?? 'suporte';

        // Share with views
        view()->share('aiModule', $module);

        // Add to request
        $request->merge(['ai_module' => $module]);

        return $next($request);
    }
}

