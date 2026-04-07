<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AccountPayable;

class AccountsPayableController extends Controller
{
    public function index()
    {
        return view('system.desenvolvimento', [
            'title'       => 'Contas a Pagar',
            'description' => 'Controle de pagamentos, vencimentos e obrigações financeiras',
            'color'       => '#22C55E',
            'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>',
            'moduleSlug'  => 'financeiro',
            'moduleName'  => 'Financeiro',
        ]);
    }

    public function create()
    {
        return view('system.desenvolvimento', [
            'title'      => 'Nova Conta a Pagar',
            'color'      => '#22C55E',
            'moduleSlug' => 'financeiro',
            'moduleName' => 'Financeiro',
        ]);
    }

    public function store(Request $request)
    {
        return redirect()->route('accounts_payable.index');
    }
}
