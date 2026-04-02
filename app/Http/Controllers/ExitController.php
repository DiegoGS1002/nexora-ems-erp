<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExitController extends Controller
{
    public function index()
    {
        return view('system.desenvolvimento', [
            'title'       => 'Notas Fiscais de Saída',
            'description' => 'Gestão de notas fiscais de saída',
            'color'       => '#EC4899',
            'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="8 16 12 12 16 16"/><line x1="12" y1="21" x2="12" y2="12"/><path d="M3.51 15a9 9 0 1 0 .49-4.95"/></svg>',
            'moduleSlug'  => 'fiscal',
            'moduleName'  => 'Fiscal',
        ]);
    }
}
