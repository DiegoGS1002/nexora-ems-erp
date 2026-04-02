<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SalesReportController extends Controller
{
    public function index()
    {
        return view('system.desenvolvimento', [
            'title'       => 'Relatórios de Vendas',
            'description' => 'Análise e relatórios de desempenho comercial',
            'color'       => '#06B6D4',
            'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>',
            'moduleSlug'  => 'vendas',
            'moduleName'  => 'Vendas',
        ]);
    }

    public function print() { abort(501); }
}
