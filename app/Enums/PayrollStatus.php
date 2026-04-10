<?php

namespace App\Enums;

enum PayrollStatus: string
{
    case Draft  = 'draft';
    case Closed = 'closed';
    case Paid   = 'paid';

    public function label(): string
    {
        return match($this) {
            self::Draft  => 'Rascunho',
            self::Closed => 'Fechada',
            self::Paid   => 'Paga',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Draft  => 'nx-badge-neutral',
            self::Closed => 'nx-badge-warning',
            self::Paid   => 'nx-badge-success',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Draft  => '#64748B',
            self::Closed => '#D97706',
            self::Paid   => '#15803D',
        };
    }
}

