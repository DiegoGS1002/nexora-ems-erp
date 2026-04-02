<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('system.desenvolvimento', [
            'title'       => 'Dashboard',
            'description' => 'Painel de controle e indicadores gerais de desempenho do sistema',
            'color'       => '#3B82F6',
            'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>',
            'moduleSlug'  => 'dashboard',
            'moduleName'  => 'Dashboard',
        ]);
    }
}
