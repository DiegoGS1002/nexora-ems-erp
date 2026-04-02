<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VehicleTrackingController extends Controller
{
    public function index()
    {
        return view('system.desenvolvimento', [
            'title'       => 'Rastreamento de Veículos',
            'description' => 'Rastreamento em tempo real da posição dos veículos',
            'color'       => '#0EA5E9',
            'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 8.2c0 7.3-8 11.8-8 11.8z"/><circle cx="12" cy="10" r="3"/></svg>',
            'moduleSlug'  => 'transporte',
            'moduleName'  => 'Transporte',
        ]);
    }
}
