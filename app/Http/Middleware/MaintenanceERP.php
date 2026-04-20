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
            $request->routeIs('product-categories.*') ||
            // financeiro — módulos Livewire e resources
            $request->routeIs('plans_of_accounts.*') ||
            $request->routeIs('contas_bancarias.*') ||
            $request->routeIs('accounts_payable.*') ||
            $request->routeIs('accounts_receivable.*') ||
            $request->routeIs('cash_flow.*') ||
            $request->routeIs('baccarat_accounts.*') ||
            $request->routeIs('financial_reports.*') ||
            $request->routeIs('financialReports.*') ||
            // rh — módulos Livewire e resources
            $request->routeIs('working_day.*') ||
            $request->routeIs('payroll.*') ||
            $request->routeIs('holerite.*') ||
            $request->routeIs('stitch_beat.*') ||
            $request->routeIs('employee_management.*') ||
            $request->routeIs('rh_reports.*') ||
            $request->routeIs('rhReports.*') ||
            // dashboard
            $request->routeIs('dashboard.*') ||
            // produção
            $request->routeIs('production_orders.*') ||
            // vendas
            $request->routeIs('vendas.*') ||
            $request->routeIs('requests.*') ||
            $request->routeIs('visits.*') ||
            $request->routeIs('sales_report.*') ||
            $request->routeIs('salesReports.*') ||
            // estoque
            $request->routeIs('stock.*') ||
            // logística completa
            $request->routeIs('monitoring_of_deliveries.*') ||
            $request->routeIs('driver_management.*') ||
            $request->routeIs('romaneio.*') ||
            $request->routeIs('vehicle_tracking.*') ||
            $request->routeIs('vehicle_maintenance.*') ||
            $request->routeIs('transport_report.*') ||
            $request->routeIs('transportReport.*') ||
            // empresas
            $request->routeIs('companies.*')
        ) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Rotas não reconhecidas — exibe tela "Em Desenvolvimento"
        |--------------------------------------------------------------------------
        */

        return response()->view('system.desenvolvimento');
    }
}
