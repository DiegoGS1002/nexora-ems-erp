<?php

namespace App\Enums;

enum TimeRecordStatus: string
{
    case Active  = 'active';
    case Break   = 'break';
    case Absent  = 'absent';
    case Completed = 'completed';

    public function label(): string
    {
        return match($this) {
            self::Active    => 'Presente',
            self::Break     => 'Em Pausa',
            self::Absent    => 'Ausente',
            self::Completed => 'Concluído',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Active    => '#10B981',
            self::Break     => '#F59E0B',
            self::Absent    => '#EF4444',
            self::Completed => '#6366F1',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Active    => 'nx-badge-success',
            self::Break     => 'nx-badge-warning',
            self::Absent    => 'nx-badge-danger',
            self::Completed => 'nx-badge-purple',
        };
    }
}

