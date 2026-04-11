<?php

namespace App\Services;

use App\Enums\TimeRecordStatus;
use App\Models\Employees;
use App\Models\TimeRecord;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PontoService
{
    public function getTodayRecord(string $employeeId): ?TimeRecord
    {
        return TimeRecord::where('employee_id', $employeeId)
            ->whereDate('date', now()->toDateString())
            ->first();
    }

    public function getEmployeeByEmail(string $email): ?Employees
    {
        return Employees::where('email', $email)
            ->where('is_active', true)
            ->first();
    }

    public function registerClockAction(
        string $employeeId,
        string $ipAddress,
        ?float $latitude = null,
        ?float $longitude = null
    ): array {
        $today = now()->toDateString();
        $now = now();

        $record = $this->getTodayRecord($employeeId);

        if (! $record) {
            $record = TimeRecord::create([
                'employee_id' => $employeeId,
                'date' => $today,
                'clock_in' => $now,
                'status' => TimeRecordStatus::Active,
                'ip_address' => $ipAddress,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]);

            return [
                'success' => true,
                'message' => 'Entrada registrada com sucesso!',
                'action' => 'clock_in',
                'time' => $now->format('H:i'),
            ];
        }

        if ($this->isRecentAction($record)) {
            return [
                'success' => false,
                'message' => 'Aguarde pelo menos 1 minuto antes de registrar novamente.',
                'action' => null,
            ];
        }

        if (! $record->break_start) {
            $record->update([
                'break_start' => $now,
                'status' => TimeRecordStatus::Break,
            ]);

            return [
                'success' => true,
                'message' => 'Início do intervalo registrado!',
                'action' => 'break_start',
                'time' => $now->format('H:i'),
            ];
        }

        if (! $record->break_end) {
            $record->update([
                'break_end' => $now,
                'status' => TimeRecordStatus::Active,
            ]);

            return [
                'success' => true,
                'message' => 'Retorno do intervalo registrado!',
                'action' => 'break_end',
                'time' => $now->format('H:i'),
            ];
        }

        if (! $record->clock_out) {
            $record->update([
                'clock_out' => $now,
                'status' => TimeRecordStatus::Completed,
            ]);

            return [
                'success' => true,
                'message' => 'Saída registrada com sucesso!',
                'action' => 'clock_out',
                'time' => $now->format('H:i'),
            ];
        }

        return [
            'success' => false,
            'message' => 'Todos os registros do dia já foram realizados.',
            'action' => null,
        ];
    }

    private function isRecentAction(TimeRecord $record): bool
    {
        $lastAction = collect([
            $record->clock_in,
            $record->break_start,
            $record->break_end,
            $record->clock_out,
        ])->filter()->max();

        if (! $lastAction) {
            return false;
        }

        return $lastAction->diffInSeconds(now()) < 60;
    }

    public function getNextAction(TimeRecord $record): string
    {
        if (! $record->break_start) {
            return 'Iniciar Intervalo';
        }

        if (! $record->break_end) {
            return 'Retornar do Intervalo';
        }

        if (! $record->clock_out) {
            return 'Registrar Saída';
        }

        return 'Jornada Completa';
    }

    public function getTodayRecords(string $employeeId): Collection
    {
        $record = $this->getTodayRecord($employeeId);

        if (! $record) {
            return collect([]);
        }

        $records = collect();

        if ($record->clock_in) {
            $records->push([
                'type' => 'Entrada',
                'time' => $record->clock_in->format('H:i'),
                'icon' => 'login',
            ]);
        }

        if ($record->break_start) {
            $records->push([
                'type' => 'Início Intervalo',
                'time' => $record->break_start->format('H:i'),
                'icon' => 'pause',
            ]);
        }

        if ($record->break_end) {
            $records->push([
                'type' => 'Retorno Intervalo',
                'time' => $record->break_end->format('H:i'),
                'icon' => 'play',
            ]);
        }

        if ($record->clock_out) {
            $records->push([
                'type' => 'Saída',
                'time' => $record->clock_out->format('H:i'),
                'icon' => 'logout',
            ]);
        }

        return $records;
    }

    public function calculateExpectedEndTime(TimeRecord $record, int $workHours = 8): ?string
    {
        if (! $record->clock_in) {
            return null;
        }

        $start = $record->clock_in->copy();
        $breakMinutes = 0;

        if ($record->break_start && $record->break_end) {
            $breakMinutes = $record->break_start->diffInMinutes($record->break_end);
        }

        $expectedEnd = $start->addHours($workHours)->addMinutes($breakMinutes);

        return $expectedEnd->format('H:i');
    }
}

