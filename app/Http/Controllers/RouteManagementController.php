<?php

namespace App\Http\Controllers;

use App\Models\RouteManagement;
use Illuminate\Http\Request;

class RouteManagementController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $routes = RouteManagement::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('logistica.route_management.index', [
            'routes' => $routes,
            'search' => $search,
            'stats' => [
                'total' => RouteManagement::count(),
                'withDescription' => RouteManagement::whereNotNull('description')
                    ->where('description', '!=', '')
                    ->count(),
                'createdToday' => RouteManagement::whereDate('created_at', today())->count(),
            ],
            'routingTypes' => [
                'Manual',
                'Automatica',
                'Semi-automatica',
            ],
            'parameters' => [
                'Distancia entre pontos',
                'Tempo estimado',
                'Capacidade do veiculo',
                'Janela de entrega',
                'Prioridade do pedido',
                'Regiao de entrega',
            ],
        ]);
    }
}
