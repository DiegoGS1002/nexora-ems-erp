<?php

use App\Models\RouteManagement;
use App\Models\User;

// ── Gestão de Rotas ──────────────────────────────────────────────

it('renders route management page without development placeholder', function () {
    $this->actingAs(User::factory()->create(['last_login_at' => now()]));

    $this->get(route('route_management.index'))
        ->assertOk()
        ->assertSee('Gestao de Rotas')
        ->assertDontSee('Funcionalidade em Desenvolvimento');
});

it('filters route list by search term', function () {
    RouteManagement::create(['name' => 'Rota Norte', 'description' => 'Entregas urbanas']);
    RouteManagement::create(['name' => 'Rota Sul',   'description' => 'Interior']);

    $this->actingAs(User::factory()->create(['last_login_at' => now()]));

    $this->get(route('route_management.index', ['search' => 'Norte']))
        ->assertOk()
        ->assertSee('Rota Norte')
        ->assertDontSee('Rota Sul');
});

// ── Roteirização ──────────────────────────────────────────────────

it('renders routing page without development placeholder', function () {
    $this->actingAs(User::factory()->create(['last_login_at' => now()]));

    $this->get(route('routing.index'))
        ->assertOk()
        ->assertSee('Roteirizacao de Entregas')
        ->assertDontSee('Funcionalidade em Desenvolvimento');
});

it('routing page shows api key notice when key is not configured', function () {
    config(['services.google_maps.key' => '']);

    $this->actingAs(User::factory()->create(['last_login_at' => now()]));

    $this->get(route('routing.index'))
        ->assertOk()
        ->assertSee('GOOGLE_MAPS_API_KEY');
});

it('routing page shows map when api key is configured', function () {
    config(['services.google_maps.key' => 'test-api-key-123']);

    $this->actingAs(User::factory()->create(['last_login_at' => now()]));

    $this->get(route('routing.index'))
        ->assertOk()
        ->assertSee('routing-map')
        ->assertSee('test-api-key-123');
});

