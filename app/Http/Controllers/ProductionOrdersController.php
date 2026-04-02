<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductionOrdersController extends Controller
{
    public function index()
    {
        return view('system.desenvolvimento', [
            'title'       => 'Ordens de Produção',
            'description' => 'Criação e controle das ordens de produção',
            'color'       => '#F59E0B',
            'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>',
            'moduleSlug'  => 'producao',
            'moduleName'  => 'Produção',
        ]);
    }
}
