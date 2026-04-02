<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MonitoringOfDeliveriesController extends Controller
{
    public function index()
    {
        return view('system.desenvolvimento', [
            'title'       => 'Monitoramento de Entregas',
            'description' => 'Rastreamento e acompanhamento das entregas em tempo real',
            'color'       => '#0EA5E9',
            'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M1 6v16l7-4 8 4 7-4V2l-7 4-8-4-7 4z"/><line x1="8" y1="2" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="22"/></svg>',
            'moduleSlug'  => 'transporte',
            'moduleName'  => 'Transporte',
        ]);
    }
}
