<?php

namespace App\Enums;

enum PayableStatus: string
{
    case Pending   = 'pending';
    case Paid      = 'paid';
    case Overdue   = 'overdue';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Pending   => 'Pendente',
            self::Paid      => 'Pago',
            self::Overdue   => 'Vencido',
            self::Cancelled => 'Cancelado',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Pending   => 'nx-badge-warning',
            self::Paid      => 'nx-badge-success',
            self::Overdue   => 'nx-badge-danger',
            self::Cancelled => 'nx-badge-neutral',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Pending   => '#D97706',
            self::Paid      => '#15803D',
            self::Overdue   => '#B91C1C',
            self::Cancelled => '#475569',
        };
    }
}

