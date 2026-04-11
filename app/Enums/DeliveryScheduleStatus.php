<?php

namespace App\Enums;

enum DeliveryScheduleStatus: string
{
    case Pendente      = 'pendente';
    case Agendado      = 'agendado';
    case Confirmado    = 'confirmado';
    case EmSeparacao   = 'em_separacao';
    case EmRota        = 'em_rota';
    case Entregue      = 'entregue';
    case NaoEntregue   = 'nao_entregue';
    case Reagendado    = 'reagendado';
    case Cancelado     = 'cancelado';

    public function label(): string
    {
        return match($this) {
            self::Pendente    => 'Pendente',
            self::Agendado    => 'Agendado',
            self::Confirmado  => 'Confirmado',
            self::EmSeparacao => 'Em Separação',
            self::EmRota      => 'Em Rota',
            self::Entregue    => 'Entregue',
            self::NaoEntregue => 'Não Entregue',
            self::Reagendado  => 'Reagendado',
            self::Cancelado   => 'Cancelado',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Pendente    => '#94A3B8',
            self::Agendado    => '#3B82F6',
            self::Confirmado  => '#6366F1',
            self::EmSeparacao => '#F59E0B',
            self::EmRota      => '#0EA5E9',
            self::Entregue    => '#10B981',
            self::NaoEntregue => '#EF4444',
            self::Reagendado  => '#F97316',
            self::Cancelado   => '#DC2626',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Pendente    => 'ds-badge--pendente',
            self::Agendado    => 'ds-badge--agendado',
            self::Confirmado  => 'ds-badge--confirmado',
            self::EmSeparacao => 'ds-badge--em-separacao',
            self::EmRota      => 'ds-badge--em-rota',
            self::Entregue    => 'ds-badge--entregue',
            self::NaoEntregue => 'ds-badge--nao-entregue',
            self::Reagendado  => 'ds-badge--reagendado',
            self::Cancelado   => 'ds-badge--cancelado',
        };
    }
}

