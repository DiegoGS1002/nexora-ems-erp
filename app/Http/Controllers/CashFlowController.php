<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CashFlowController extends Controller
{
    public function index()
    {
        return view('system.desenvolvimento', [
            'title'       => 'Fluxo de Caixa',
            'description' => 'Projeção e análise do fluxo de caixa da empresa',
            'color'       => '#22C55E',
            'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>',
            'moduleSlug'  => 'financeiro',
            'moduleName'  => 'Financeiro',
        ]);
    }
}
