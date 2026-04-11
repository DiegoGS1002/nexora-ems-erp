<?php

namespace App\Enums;

enum ProductionOrderStatus: string
{
    case Planned    = 'planned';
    case InProgress = 'in_progress';
    case Paused     = 'paused';
    case Completed  = 'completed';
    case Cancelled  = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Planned    => 'Planejada',
            self::InProgress => 'Em Produção',
            self::Paused     => 'Pausada',
            self::Completed  => 'Finalizada',
            self::Cancelled  => 'Cancelada',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Planned    => 'nx-op-badge--planned',
            self::InProgress => 'nx-op-badge--in-progress',
            self::Paused     => 'nx-op-badge--paused',
            self::Completed  => 'nx-op-badge--completed',
            self::Cancelled  => 'nx-op-badge--cancelled',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Planned    => '#6366F1',
            self::InProgress => '#F59E0B',
            self::Paused     => '#94A3B8',
            self::Completed  => '#10B981',
            self::Cancelled  => '#EF4444',
        };
    }
}

