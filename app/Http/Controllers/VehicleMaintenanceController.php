<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VehicleMaintenanceController extends Controller
{
    public function index()
    {
        return view('system.desenvolvimento', [
            'title'       => 'Manutenção de Veículos',
            'description' => 'Controle de manutenções preventivas e corretivas da frota',
            'color'       => '#0EA5E9',
            'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>',
            'moduleSlug'  => 'transporte',
            'moduleName'  => 'Transporte',
        ]);
    }
}
