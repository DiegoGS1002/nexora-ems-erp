# Módulo de Gestão de Rotas

## 1. Visão Geral

Otimiza rotas de entrega, agrupando múltiplos pedidos em uma rota única para economizar tempo e combustível.

### Funcionalidades
- Criação de rotas com múltiplas entregas
- Sequenciamento otimizado
- Cálculo de distância
- Custo estimado
- Acompanhamento em tempo real (GPS)

---

## 2. Estrutura

### Model: `DeliveryRoute`
```php
protected $fillable = [
    'route_number',
    'vehicle_id',
    'driver_id',
    'date',
    'status', // pending, in_progress, completed
    'total_distance_km',
    'estimated_cost',
    'actual_cost',
    'start_time',
    'end_time',
];
```

### Paradas (Entregas)
```php
RouteStop::create([
    'delivery_route_id' => $route->id,
    'scheduling_of_delivery_id' => $agendamento->id,
    'sequence_order' => 1, // Ordem de visita
    'estimated_arrival' => '08:30',
    'actual_arrival' => '08:45',
    'status' => 'delivered',
]);
```

### Otimização
```php
// Algoritmo simples: ordena por CEP ou proximidade
$route->stops()->orderBy('sequence_order')->get();
```

---

**Caminho**: `app/Models/DeliveryRoute.php`, `app/Livewire/Logistica/GestaoRotas.php`

