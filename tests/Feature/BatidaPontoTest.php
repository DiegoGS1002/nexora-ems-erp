<?php

use App\Livewire\Rh\BatidaPonto;
use App\Models\Employees;
use App\Models\TimeRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'joao@example.com',
        'is_active' => true,
        'last_login_at' => now(),
    ]);

    $this->employee = Employees::create([
        'name' => 'João Silva',
        'identification_number' => '12345678900',
        'role' => 'Desenvolvedor',
        'email' => 'joao@example.com',
        'phone_number' => '11999999999',
        'address' => 'Rua Teste, 123',
        'is_active' => true,
    ]);
});

it('redirects to login if not authenticated', function () {
    $this->get(route('stitch_beat.index'))
        ->assertRedirect(route('login'));
});

it('renders successfully when authenticated', function () {
    $this->actingAs($this->user);

    $this->get(route('stitch_beat.index'))
        ->assertOk()
        ->assertSeeLivewire(BatidaPonto::class);
});

it('shows employee information', function () {
    $this->actingAs($this->user);

    Livewire::test(BatidaPonto::class)
        ->assertSee('João Silva')
        ->assertSee('Desenvolvedor')
        ->assertSee('Registrar Entrada');
});

it('shows error if user has no employee record', function () {
    $userWithoutEmployee = User::factory()->create([
        'email' => 'noemp@example.com',
        'last_login_at' => now(),
    ]);

    $this->actingAs($userWithoutEmployee);

    Livewire::test(BatidaPonto::class)
        ->assertSee('Você não está cadastrado como funcionário no sistema');
});

it('can register clock in', function () {
    $this->actingAs($this->user);

    Livewire::test(BatidaPonto::class)
        ->call('registerPonto')
        ->assertDispatched('show-success');

    expect(TimeRecord::where('employee_id', $this->employee->id)->exists())->toBeTrue();
});

it('displays today records', function () {
    $this->actingAs($this->user);

    TimeRecord::create([
        'employee_id' => $this->employee->id,
        'date' => now()->toDateString(),
        'clock_in' => now()->setTime(8, 0),
        'status' => \App\Enums\TimeRecordStatus::Active,
    ]);

    Livewire::test(BatidaPonto::class)
        ->assertSee('Batidas de Hoje')
        ->assertSee('Entrada')
        ->assertSee('08:00');
});

it('shows journey complete when all records are done', function () {
    $this->actingAs($this->user);

    TimeRecord::create([
        'employee_id' => $this->employee->id,
        'date' => now()->toDateString(),
        'clock_in' => now()->setTime(8, 0),
        'break_start' => now()->setTime(12, 0),
        'break_end' => now()->setTime(13, 0),
        'clock_out' => now()->setTime(17, 0),
        'status' => \App\Enums\TimeRecordStatus::Completed,
    ]);

    Livewire::test(BatidaPonto::class)
        ->assertSee('Jornada Completa')
        ->assertDontSee('Registrar Entrada');
});

it('updates location when provided', function () {
    $this->actingAs($this->user);

    Livewire::test(BatidaPonto::class)
        ->call('updateLocation', -23.5505, -46.6333, 'São Paulo, SP')
        ->assertSet('latitude', -23.5505)
        ->assertSet('longitude', -46.6333)
        ->assertSet('locationStatus', 'success');
});

it('handles location denied', function () {
    $this->actingAs($this->user);

    Livewire::test(BatidaPonto::class)
        ->call('locationDenied')
        ->assertSet('locationStatus', 'denied')
        ->assertSet('locationName', 'Localização não autorizada');
});

it('shows expected end time when record exists', function () {
    $this->actingAs($this->user);

    TimeRecord::create([
        'employee_id' => $this->employee->id,
        'date' => now()->toDateString(),
        'clock_in' => now()->setTime(8, 0),
        'status' => \App\Enums\TimeRecordStatus::Active,
    ]);

    Livewire::test(BatidaPonto::class)
        ->assertSee('Previsão de Término');
});

