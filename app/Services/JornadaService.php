<?php

namespace App\Services;

use App\Enums\TimeRecordStatus;
use App\Models\Employees;
use App\Models\TimeRecord;
use App\Models\WorkShift;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class JornadaService
{
    public function getKpis(string $date): array
    {
        $records = TimeRecord::whereDate('date', $date)->get();
        $totalEmployees = Employees::where('is_active', true)->count();

        $present   = $records->where('status', TimeRecordStatus::Active)->count();
        $onBreak   = $records->where('status', TimeRecordStatus::Break)->count();
        $completed = $records->where('status', TimeRecordStatus::Completed)->count();
        $absent    = $records->where('status', TimeRecordStatus::Absent)->count();
        $registered = $present + $onBreak + $completed + $absent;
        $notRegistered = max(0, $totalEmployees - $registered);

        $totalBankMinutes = TimeRecord::whereDate('date', $date)
            ->whereNotNull('clock_out')
            ->get()
            ->sum(fn (TimeRecord $r) => max(0, $r->worked_minutes - 480));

        return [
            'total_employees' => $totalEmployees,
            'present'         => $present + $onBreak + $completed,
            'on_break'        => $onBreak,
            'absent'          => $absent + $notRegistered,
            'bank_hours'      => sprintf('%02dh%02d', intdiv($totalBankMinutes, 60), $totalBankMinutes % 60),
        ];
    }

    public function getPresenceGrid(string $date): Collection
    {
        $records = TimeRecord::with(['employee', 'workShift'])
            ->whereDate('date', $date)
            ->get()
            ->keyBy('employee_id');

        return Employees::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function (Employees $emp) use ($records) {
                $record = $records->get($emp->id);

                return (object) [
                    'employee'       => $emp,
                    'status'         => $record?->status ?? TimeRecordStatus::Absent,
                    'clock_in_time'  => $record?->clock_in?->format('H:i') ?? '--:--',
                    'break_time'     => $record?->break_start?->format('H:i') ?? '--:--',
                    'clock_out_time' => $record?->clock_out?->format('H:i') ?? '--:--',
                    'shift_name'     => $record?->workShift?->name ?? $emp->work_schedule ?? '--',
                    'worked_hours'   => $record?->worked_hours_formatted ?? '--:--',
                    'record_id'      => $record?->id,
                ];
            });
    }

    public function getTimelineRecords(string $date): Collection
    {
        return TimeRecord::with(['employee', 'workShift'])
            ->whereDate('date', $date)
            ->whereNotNull('clock_in')
            ->orderBy('clock_in')
            ->get();
    }

    public function saveRecord(array $data): TimeRecord
    {
        $dateTime = fn (?string $time) => $time
            ? Carbon::createFromFormat('Y-m-d H:i', "{$data['date']} {$time}")
            : null;

        $existing = TimeRecord::where('employee_id', $data['employee_id'])
            ->whereDate('date', $data['date'])
            ->first();

        $payload = [
            'employee_id'   => $data['employee_id'],
            'work_shift_id' => $data['work_shift_id'] ?: null,
            'date'          => $data['date'],
            'clock_in'      => $dateTime($data['clock_in'] ?: null),
            'break_start'   => $dateTime($data['break_start'] ?: null),
            'break_end'     => $dateTime($data['break_end'] ?: null),
            'clock_out'     => $dateTime($data['clock_out'] ?: null),
            'status'        => $data['status'],
            'observation'   => $data['observation'] ?? null,
        ];

        if ($existing) {
            $existing->update($payload);
            return $existing->fresh();
        }

        return TimeRecord::create($payload);
    }

    public function deleteRecord(int $id): void
    {
        TimeRecord::where('id', $id)->delete();
    }

    public function getActiveShifts(): Collection
    {
        return WorkShift::where('is_active', true)->orderBy('name')->get();
    }
}

