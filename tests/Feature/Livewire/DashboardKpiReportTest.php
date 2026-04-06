<?php

use App\Livewire\Dashboard\KpiReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(\App\Models\User::factory()->create(['last_login_at' => now()]));
});

it('renders dashboard kpi route', function () {
    $response = $this->get(route('dashboard.kpi'));

    $response->assertOk();
    $response->assertSee('Indicadores KPI');
});

it('loads kpi report payload', function () {
    Livewire::test(KpiReport::class)
        ->assertSet('faturamento', [12000, 19000, 30000, 50000])
        ->assertSet('categorias', ['Jan', 'Fev', 'Mar', 'Abr'])
        ->assertSet('distribuicao', [34, 28, 20, 18])
        ->assertSet('tableRows', fn (array $value) => count($value) === 4)
        ->assertSet('kpis.0.title', 'Faturamento')
        ->assertSet('kpis.0.value', 128590)
        ->assertSet('kpis.3.title', 'Despesas');
});

it('filters table by month event', function () {
    Livewire::test(KpiReport::class)
        ->call('filtrarMes', 0)
        ->assertSet('selectedMonth', 0)
        ->assertViewHas('rows', fn (array $rows) => count($rows) <= 1);
});

