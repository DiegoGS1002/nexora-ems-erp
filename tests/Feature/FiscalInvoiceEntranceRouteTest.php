<?php

use App\Livewire\Fiscal\NotaFiscalEntrada;
use App\Models\User;
use Illuminate\Support\Facades\Route;

it('exposes the functional fiscal invoice entrance component', function () {
    $route = Route::getRoutes()->getByName('fiscal.entrance.index');

    expect($route)->not->toBeNull()
        ->and($route->uri())->toBe('fiscal/entrada')
        ->and($route->getActionName())->toBe(NotaFiscalEntrada::class)
        ->and(Route::has('fiscal.entrance.create'))->toBeFalse()
        ->and(Route::has('fiscal.entrance.store'))->toBeFalse();
});

it('redirects guests from the fiscal invoice entrance page to login', function () {
    $this->get(route('fiscal.entrance.index'))
        ->assertRedirect(route('login'));
});

it('renders the fiscal invoice entrance component for an authorized user', function () {
    $user = User::factory()->create([
        'is_admin' => true,
        'email' => 'admin@nexora.local',
        'last_login_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('fiscal.entrance.index'))
        ->assertOk()
        ->assertSeeLivewire(NotaFiscalEntrada::class)
        ->assertDontSee('Funcionalidade em Desenvolvimento');
});
