<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RouteManagementController extends Controller
{
    public function index()
    {
        return view('system.desenvolvimento', [
            'title'       => 'Gestão de Rotas',
            'description' => 'Administração e cadastro de rotas de transporte',
            'color'       => '#0EA5E9',
            'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="3" cy="12" r="2"/><circle cx="21" cy="5" r="2"/><circle cx="21" cy="19" r="2"/><line x1="5" y1="12" x2="19" y2="5.5"/><line x1="5" y1="12" x2="19" y2="18.5"/></svg>',
            'moduleSlug'  => 'transporte',
            'moduleName'  => 'Transporte',
        ]);
    }
}
