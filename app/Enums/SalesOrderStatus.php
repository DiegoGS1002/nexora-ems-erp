<?php

namespace App\Enums;

enum SalesOrderStatus: string
{
    case Draft        = 'draft';
    case Aberto       = 'aberto';
    case Approved     = 'approved';
    case EmSeparacao  = 'em_separacao';
    case Invoiced     = 'invoiced';
    case Delivered    = 'delivered';
    case Cancelled    = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Draft       => 'Rascunho',
            self::Aberto      => 'Aberto',
            self::Approved    => 'Aprovado',
            self::EmSeparacao => 'Em Separação',
            self::Invoiced    => 'Faturado',
            self::Delivered   => 'Entregue',
            self::Cancelled   => 'Cancelado',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Draft       => 'nx-so-badge--draft',
            self::Aberto      => 'nx-so-badge--open',
            self::Approved    => 'nx-so-badge--approved',
            self::EmSeparacao => 'nx-so-badge--picking',
            self::Invoiced    => 'nx-so-badge--invoiced',
            self::Delivered   => 'nx-so-badge--delivered',
            self::Cancelled   => 'nx-so-badge--cancelled',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Draft       => '#94A3B8',
            self::Aberto      => '#06B6D4',
            self::Approved    => '#3B82F6',
            self::EmSeparacao => '#F59E0B',
            self::Invoiced    => '#8B5CF6',
            self::Delivered   => '#10B981',
            self::Cancelled   => '#EF4444',
        };
    }
}

