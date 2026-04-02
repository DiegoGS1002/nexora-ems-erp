<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RequestsController extends Controller
{
    public function index()
    {
        return view('system.desenvolvimento', [
            'title'       => 'Pedidos',
            'description' => 'Gerenciamento de pedidos de venda',
            'color'       => '#06B6D4',
            'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>',
            'moduleSlug'  => 'vendas',
            'moduleName'  => 'Vendas',
        ]);
    }
}
