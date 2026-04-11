<?php

namespace App\Http\Controllers;

class RoutingController extends Controller
{
    public function index()
    {
        return view('logistica.routing.index', [
            'mapsApiKey' => config('services.google_maps.key', ''),
        ]);
    }
}
