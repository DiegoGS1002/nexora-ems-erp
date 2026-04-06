<?php

use App\Livewire\Dashboard\Overview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(\App\Models\User::factory()->create(['last_login_at' => now()]));
});

it('renders dashboard overview route', function () {
    $response = $this->get(route('dashboard.index'));

    $response->assertOk();
    $response->assertSee('Dashboard');
});

it('loads overview component with kpis', function () {
    Livewire::test(Overview::class)
        ->assertSet('kpis', fn (array $kpis) => count($kpis) === 4)
        ->assertSet('kpis.0.title', 'Faturamento')
        ->assertSet('kpis.0.value', 128590)
        ->assertSet('kpis.1.title', 'Produtos')
        ->assertSet('kpis.2.title', 'Pedidos')
        ->assertSet('kpis.3.title', 'Despesas')
        ->assertSet('faturamento', [12000, 19000, 30000, 50000])
        ->assertSet('categorias', ['Jan', 'Fev', 'Mar', 'Abr']);
});

