<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index()
    {
        return view('system.desenvolvimento', [
            'title'       => 'Folha de Pagamento',
            'description' => 'Processamento e gestão da folha de pagamento salarial',
            'color'       => '#A78BFA',
            'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>',
            'moduleSlug'  => 'rh',
            'moduleName'  => 'Recursos Humanos',
        ]);
    }
}
