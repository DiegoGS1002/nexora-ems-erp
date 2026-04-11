<?php

use Illuminate\Support\Facades\Route;

// Test if routes exist
$routes = [
    'fiscal.tipo-operacao.index',
    'fiscal.tipo-operacao.create',
    'fiscal.tipo-operacao.edit',
    'fiscal.grupo-tributario.index',
    'fiscal.grupo-tributario.create',
    'fiscal.grupo-tributario.edit',
];

echo "🔍 Verificando se rotas existem:\n\n";

foreach ($routes as $routeName) {
    $exists = Route::has($routeName);
    $status = $exists ? '✅ EXISTE' : '❌ NÃO EXISTE';
    echo "{$status} - {$routeName}\n";

    if ($exists) {
        try {
            $url = route($routeName, ['operacao' => 1, 'grupo' => 1], false);
            echo "   URL: {$url}\n";
        } catch (\Exception $e) {
            $url = route($routeName, [], false);
            echo "   URL: {$url}\n";
        }
    }
    echo "\n";
}

