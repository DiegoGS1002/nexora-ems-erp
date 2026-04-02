<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DriverManagementController extends Controller
{
    public function index()
    {
        return view('system.desenvolvimento', [
            'title'       => 'Gestão de Motoristas',
            'description' => 'Cadastro e gerenciamento dos motoristas da frota',
            'color'       => '#0EA5E9',
            'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
            'moduleSlug'  => 'transporte',
            'moduleName'  => 'Transporte',
        ]);
    }
}
