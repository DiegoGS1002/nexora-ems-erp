<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccountsReceivableController extends Controller
{
    public function index()
    {
        return view('system.desenvolvimento', [
            'title'       => 'Contas a Receber',
            'description' => 'Controle de recebimentos, cobranças e créditos',
            'color'       => '#22C55E',
            'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>',
            'moduleSlug'  => 'financeiro',
            'moduleName'  => 'Financeiro',
        ]);
    }

    public function create()
    {
        return view('system.desenvolvimento', [
            'title'      => 'Nova Conta a Receber',
            'color'      => '#22C55E',
            'moduleSlug' => 'financeiro',
            'moduleName' => 'Financeiro',
        ]);
    }

    public function store(Request $request)
    {
        return redirect()->route('accounts_receivable.index');
    }
}
