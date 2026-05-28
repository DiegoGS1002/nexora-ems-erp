<?php

namespace Database\Seeders;

use App\Models\WorkShift;
use Illuminate\Database\Seeder;

class WorkShiftSeeder extends Seeder
{
    public function run(): void
    {
        if (WorkShift::count() > 0) {
            $this->command->warn('Jornadas de trabalho já existem. Seeder ignorado.');
            return;
        }

        $shifts = [
            [
                'name'           => 'Turno Administrativo',
                'description'    => 'Jornada administrativa padrão das 8h às 18h',
                'start_time'     => '08:00',
                'end_time'       => '18:00',
                'break_duration' => 60,
                'is_active'      => true,
            ],
            [
                'name'           => 'Turno A - Manhã',
                'description'    => 'Primeiro turno operacional das 6h às 14h',
                'start_time'     => '06:00',
                'end_time'       => '14:00',
                'break_duration' => 60,
                'is_active'      => true,
            ],
            [
                'name'           => 'Turno B - Tarde',
                'description'    => 'Segundo turno operacional das 14h às 22h',
                'start_time'     => '14:00',
                'end_time'       => '22:00',
                'break_duration' => 60,
                'is_active'      => true,
            ],
            [
                'name'           => 'Turno C - Noite',
                'description'    => 'Turno noturno das 22h às 6h',
                'start_time'     => '22:00',
                'end_time'       => '06:00',
                'break_duration' => 60,
                'is_active'      => true,
            ],
            [
                'name'           => 'Turno Madrugada - Padaria',
                'description'    => 'Produção de padaria das 3h às 11h',
                'start_time'     => '03:00',
                'end_time'       => '11:00',
                'break_duration' => 30,
                'is_active'      => true,
            ],
            [
                'name'           => 'Meio Período - Manhã',
                'description'    => 'Jornada parcial das 8h às 12h',
                'start_time'     => '08:00',
                'end_time'       => '12:00',
                'break_duration' => 0,
                'is_active'      => true,
            ],
            [
                'name'           => 'Meio Período - Tarde',
                'description'    => 'Jornada parcial das 13h às 17h',
                'start_time'     => '13:00',
                'end_time'       => '17:00',
                'break_duration' => 0,
                'is_active'      => true,
            ],
        ];

        foreach ($shifts as $shift) {
            WorkShift::create($shift);
        }

        $this->command->info('✅ ' . count($shifts) . ' jornadas de trabalho criadas.');
    }
}

