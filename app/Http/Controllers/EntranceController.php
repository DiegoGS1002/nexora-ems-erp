<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EntranceController extends Controller
{
    public function index()
    {
        return view('system.desenvolvimento', [
            'title'       => 'Notas Fiscais de Entrada',
            'description' => 'Gestão de notas fiscais de entrada e NF-e',
            'color'       => '#EC4899',
            'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/></svg>',
            'moduleSlug'  => 'fiscal',
            'moduleName'  => 'Fiscal',
        ]);
    }
}
