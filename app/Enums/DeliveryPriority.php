<?php

namespace App\Enums;

enum DeliveryPriority: string
{
    case Normal    = 'normal';
    case Urgente   = 'urgente';
    case Agendada  = 'agendada';

    public function label(): string
    {
        return match($this) {
            self::Normal   => 'Normal',
            self::Urgente  => 'Urgente',
            self::Agendada => 'Agendada',
        };
    }
}

