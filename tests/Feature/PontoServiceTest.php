<?php

use App\Enums\TimeRecordStatus;
use App\Models\Employees;
use App\Models\TimeRecord;
use App\Services\PontoService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = new PontoService();

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

it('can get employee by email', function () {
    $employee = $this->service->getEmployeeByEmail('joao@example.com');

    expect($employee)->not->toBeNull()
        ->and($employee->name)->toBe('João Silva');
});

it('returns null for inactive employee', function () {
    $this->employee->update(['is_active' => false]);

    $employee = $this->service->getEmployeeByEmail('joao@example.com');

    expect($employee)->toBeNull();
});

it('can register clock in', function () {
    $result = $this->service->registerClockAction(
        $this->employee->id,
        '127.0.0.1',
        -23.5505,
        -46.6333
    );

    expect($result['success'])->toBeTrue()
        ->and($result['action'])->toBe('clock_in')
        ->and($result['message'])->toContain('Entrada registrada');

    $record = TimeRecord::where('employee_id', $this->employee->id)->first();
    expect($record)->not->toBeNull()
        ->and($record->status)->toBe(TimeRecordStatus::Active)
        ->and($record->clock_in)->not->toBeNull()
        ->and($record->ip_address)->toBe('127.0.0.1')
        ->and((float) $record->latitude)->toEqual(-23.5505)
        ->and((float) $record->longitude)->toEqual(-46.6333);
});

it('can register break start after clock in', function () {
    TimeRecord::create([
        'employee_id' => $this->employee->id,
        'date' => now()->toDateString(),
        'clock_in' => now()->subHours(4),
        'status' => TimeRecordStatus::Active,
    ]);

    sleep(2);

    $result = $this->service->registerClockAction(
        $this->employee->id,
        '127.0.0.1'
    );

    expect($result['success'])->toBeTrue()
        ->and($result['action'])->toBe('break_start')
        ->and($result['message'])->toContain('Início do intervalo');

    $record = TimeRecord::where('employee_id', $this->employee->id)->first();
    expect($record->break_start)->not->toBeNull()
        ->and($record->status)->toBe(TimeRecordStatus::Break);
});

it('can register break end after break start', function () {
    TimeRecord::create([
        'employee_id' => $this->employee->id,
        'date' => now()->toDateString(),
        'clock_in' => now()->subHours(4),
        'break_start' => now()->subHour(),
        'status' => TimeRecordStatus::Break,
    ]);

    sleep(2);

    $result = $this->service->registerClockAction(
        $this->employee->id,
        '127.0.0.1'
    );

    expect($result['success'])->toBeTrue()
        ->and($result['action'])->toBe('break_end')
        ->and($result['message'])->toContain('Retorno do intervalo');

    $record = TimeRecord::where('employee_id', $this->employee->id)->first();
    expect($record->break_end)->not->toBeNull()
        ->and($record->status)->toBe(TimeRecordStatus::Active);
});

it('can register clock out', function () {
    TimeRecord::create([
        'employee_id' => $this->employee->id,
        'date' => now()->toDateString(),
        'clock_in' => now()->subHours(9),
        'break_start' => now()->subHours(5),
        'break_end' => now()->subHours(4),
        'status' => TimeRecordStatus::Active,
    ]);

    sleep(2);

    $result = $this->service->registerClockAction(
        $this->employee->id,
        '127.0.0.1'
    );

    expect($result['success'])->toBeTrue()
        ->and($result['action'])->toBe('clock_out')
        ->and($result['message'])->toContain('Saída registrada');

    $record = TimeRecord::where('employee_id', $this->employee->id)->first();
    expect($record->clock_out)->not->toBeNull()
        ->and($record->status)->toBe(TimeRecordStatus::Completed);
});

it('prevents duplicate actions within 1 minute', function () {
    TimeRecord::create([
        'employee_id' => $this->employee->id,
        'date' => now()->toDateString(),
        'clock_in' => now(),
        'status' => TimeRecordStatus::Active,
    ]);

    $result = $this->service->registerClockAction(
        $this->employee->id,
        '127.0.0.1'
    );

    expect($result['success'])->toBeFalse()
        ->and($result['message'])->toContain('Aguarde pelo menos 1 minuto');
});

it('prevents actions after journey is complete', function () {
    TimeRecord::create([
        'employee_id' => $this->employee->id,
        'date' => now()->toDateString(),
        'clock_in' => now()->subHours(9),
        'break_start' => now()->subHours(5),
        'break_end' => now()->subHours(4),
        'clock_out' => now()->subMinutes(5),
        'status' => TimeRecordStatus::Completed,
    ]);

    $result = $this->service->registerClockAction(
        $this->employee->id,
        '127.0.0.1'
    );

    expect($result['success'])->toBeFalse()
        ->and($result['message'])->toContain('Todos os registros do dia já foram realizados');
});

it('gets today records correctly', function () {
    TimeRecord::create([
        'employee_id' => $this->employee->id,
        'date' => now()->toDateString(),
        'clock_in' => now()->subHours(4),
        'break_start' => now()->subHour(),
        'status' => TimeRecordStatus::Break,
    ]);

    $records = $this->service->getTodayRecords($this->employee->id);

    expect($records)->toHaveCount(2)
        ->and($records[0]['type'])->toBe('Entrada')
        ->and($records[1]['type'])->toBe('Início Intervalo');
});

it('calculates expected end time correctly', function () {
    $clockIn = now()->setTime(8, 0, 0);

    $record = TimeRecord::create([
        'employee_id' => $this->employee->id,
        'date' => now()->toDateString(),
        'clock_in' => $clockIn,
        'break_start' => $clockIn->copy()->addHours(4),
        'break_end' => $clockIn->copy()->addHours(5),
        'status' => TimeRecordStatus::Active,
    ]);

    $expectedEnd = $this->service->calculateExpectedEndTime($record);

    expect($expectedEnd)->toBe('17:00');
});

it('gets next action correctly', function () {
    $record = TimeRecord::create([
        'employee_id' => $this->employee->id,
        'date' => now()->toDateString(),
        'clock_in' => now()->subHours(4),
        'status' => TimeRecordStatus::Active,
    ]);

    expect($this->service->getNextAction($record))->toBe('Iniciar Intervalo');

    $record->update(['break_start' => now(), 'status' => TimeRecordStatus::Break]);
    expect($this->service->getNextAction($record))->toBe('Retornar do Intervalo');

    $record->update(['break_end' => now(), 'status' => TimeRecordStatus::Active]);
    expect($this->service->getNextAction($record))->toBe('Registrar Saída');

    $record->update(['clock_out' => now(), 'status' => TimeRecordStatus::Completed]);
    expect($this->service->getNextAction($record))->toBe('Jornada Completa');
});

