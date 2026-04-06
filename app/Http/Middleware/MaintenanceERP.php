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
            // configurações do sistema
            $request->routeIs('configuration.*') ||
            // perfil e permissões
            $request->routeIs('profile.*') ||
            $request->routeIs('permissions.*') ||
            $request->routeIs('logs.*') ||
            // suporte
            $request->routeIs('suporte.*')
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
