<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PlansOfAccountsController extends Controller
{
    public function index()
    {
        return view('system.desenvolvimento', [
            'title'       => 'Plano de Contas',
            'description' => 'Estrutura e gestão do plano de contas contábil',
            'color'       => '#22C55E',
            'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>',
            'moduleSlug'  => 'financeiro',
            'moduleName'  => 'Financeiro',
        ]);
    }
}
