<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MaintenanceERP
{
    public function handle(Request $request, Closure $next)
    {
        /*
        |--------------------------------------------------------------------------
        | ROTAS LIBERADAS (NÃO MOSTRAM A TELA)
        |--------------------------------------------------------------------------
        */

        if (
            // home page
            $request->routeIs('home') ||
            // cadastro resources
            $request->routeIs('products.*') ||
            $request->routeIs('clients.*') ||
            $request->routeIs('suppliers.*')
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
