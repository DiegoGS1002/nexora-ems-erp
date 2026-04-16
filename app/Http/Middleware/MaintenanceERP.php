<?php

namespace App\Http\Middleware;

use App\Http\Controllers\ModulePageController;
use Closure;
use Illuminate\Http\Request;

class MaintenanceERP
{
    public function handle(Request $request, Closure $next)
    {
        $routeName = $request->route()?->getName();

        if ($routeName && in_array($routeName, ModulePageController::moduleItemRouteNames(), true)) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | ROTAS LIBERADAS (NÃO MOSTRAM A TELA)
        |--------------------------------------------------------------------------
        */

        if (
            // home page
            $request->routeIs('home') ||
            $request->routeIs('module.show') ||
            $request->routeIs('module.item.development') ||
            // cadastro resources
            $request->routeIs('products.*') ||
            $request->routeIs('clients.*') ||
            $request->routeIs('vehicles.*') ||
            $request->routeIs('employees.*') ||
            $request->routeIs('roles.*') ||
            $request->routeIs('role.*') ||
            $request->routeIs('suppliers.*') ||
            // administracao de usuarios (somente admin)
            $request->routeIs('users.*') ||
            // empresas (multi-tenant)
            $request->routeIs('companies.*') ||
            // configurações do sistema
            $request->routeIs('configuration.*') ||
            // perfil e permissões
            $request->routeIs('profile.*') ||
            $request->routeIs('permissions.*') ||
            $request->routeIs('logs.*') ||
            // suporte
            $request->routeIs('suporte.*') ||
            // logística — rotas e roteirização
            $request->routeIs('route_management.*') ||
            $request->routeIs('routing.*') ||
            $request->routeIs('scheduling_of_deliveries.*') ||
            // compras
            $request->routeIs('compras.*') ||
            // fiscal
            $request->routeIs('fiscal.*') ||
            // notificações
            $request->routeIs('notifications.*') ||
            // unidades e categorias de produto
            $request->routeIs('unit-of-measures.*') ||
            $request->routeIs('product-categories.*')
        ) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | TODO RESTO MOSTRA A TELA
        |--------------------------------------------------------------------------
        */

        return response()->view('system.desenvolvimento');
    }
}
