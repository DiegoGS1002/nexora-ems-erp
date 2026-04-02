<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VisitsController extends Controller
{
    public function index()
    {
        return view('system.desenvolvimento', [
            'title'       => 'CRM — Visitas',
            'description' => 'Gestão de visitas e relacionamento com clientes',
            'color'       => '#06B6D4',
            'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
            'moduleSlug'  => 'vendas',
            'moduleName'  => 'Vendas',
        ]);
    }
}
