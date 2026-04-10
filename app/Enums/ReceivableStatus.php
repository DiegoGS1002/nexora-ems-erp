<?php

namespace App\Enums;

enum ReceivableStatus: string
{
    case Pending   = 'pending';
    case Received  = 'received';
    case Overdue   = 'overdue';
    case Partial   = 'partial';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Pending   => 'Pendente',
            self::Received  => 'Recebido',
            self::Overdue   => 'Vencido',
            self::Partial   => 'Parcial',
            self::Cancelled => 'Cancelado',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Pending   => 'nx-badge-warning',
            self::Received  => 'nx-badge-success',
            self::Overdue   => 'nx-badge-danger',
            self::Partial   => 'nx-badge-info',
            self::Cancelled => 'nx-badge-neutral',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Pending   => '#D97706',
            self::Received  => '#15803D',
            self::Overdue   => '#B91C1C',
            self::Partial   => '#1D4ED8',
            self::Cancelled => '#475569',
        };
    }
}

