<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductionOrdersController extends Controller
{
    public function index()
    {
        return view('producao.ordem-producao');
    }
}
