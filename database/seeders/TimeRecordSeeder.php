<?php

namespace Database\Seeders;

use App\Models\Employees;
use App\Models\TimeRecord;
use App\Models\WorkShift;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TimeRecordSeeder extends Seeder
{
    public function run(): void
    {
        if (TimeRecord::count() > 0) {
            $this->command->warn('Registros de ponto já existem. Seeder ignorado.');
            return;
        }

        $employees  = Employees::take(10)->get();
        $shiftAdmin = WorkShift::where('name', 'like', '%Administrativo%')->first();
        $shiftA     = WorkShift::where('name', 'like', '%Turno A%')->first();

        if ($employees->isEmpty()) {
            $this->command->warn('Nenhum funcionário encontrado. Execute EmployeeSeeder primeiro.');
            return;
        }

        $records  = [];
        $inserted = 0;

        // Gera registros para os últimos 15 dias úteis
        $today = Carbon::today();
        for ($d = 14; $d >= 0; $d--) {
            $date = $today->copy()->subDays($d);
            if ($date->isWeekend()) continue;

            foreach ($employees->take(5) as $employee) {
                $shift = ($employee->work_schedule === 'ADM') ? $shiftAdmin : $shiftA;
                $clockIn  = $date->copy()->setTime(8, rand(0, 15));
                $breakS   = $date->copy()->setTime(12, 0);
                $breakE   = $date->copy()->setTime(13, 0);
                $clockOut = $date->copy()->setTime(17, rand(45, 59));

                TimeRecord::create([
                    'employee_id'   => $employee->id,
                    'work_shift_id' => $shift?->id,
                    'date'          => $date->toDateString(),
                    'clock_in'      => $clockIn,
                    'break_start'   => $breakS,
                    'break_end'     => $breakE,
                    'clock_out'     => $clockOut,
                    'status'        => 'completed',
                    'ip_address'    => '192.168.1.' . rand(10, 50),
                ]);
                $inserted++;
            }
        }

        $this->command->info("✅ {$inserted} registros de ponto criados.");
    }
}


