<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WorkingDayController extends Controller
{
    public function index()
    {
        return view('system.desenvolvimento', [
            'title'       => 'Jornada de Trabalho',
            'description' => 'Configuração de jornadas, turnos e escalas de trabalho',
            'color'       => '#A78BFA',
            'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
            'moduleSlug'  => 'rh',
            'moduleName'  => 'Recursos Humanos',
        ]);
    }
}
