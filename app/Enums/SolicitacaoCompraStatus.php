<?php

namespace App\Enums;

enum SolicitacaoCompraStatus: string
{
    case Rascunho            = 'rascunho';
    case AguardandoAprovacao = 'aguardando_aprovacao';
    case Aprovada            = 'aprovada';
    case Rejeitada           = 'rejeitada';
    case Convertida          = 'convertida';
    case Cancelada           = 'cancelada';

    public function label(): string
    {
        return match($this) {
            self::Rascunho            => 'Rascunho',
            self::AguardandoAprovacao => 'Aguard. Aprovação',
            self::Aprovada            => 'Aprovada',
            self::Rejeitada           => 'Rejeitada',
            self::Convertida          => 'Convertida',
            self::Cancelada           => 'Cancelada',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Rascunho            => 'nx-so-badge--draft',
            self::AguardandoAprovacao => 'nx-so-badge--picking',
            self::Aprovada            => 'nx-so-badge--approved',
            self::Rejeitada           => 'nx-so-badge--cancelled',
            self::Convertida          => 'nx-so-badge--delivered',
            self::Cancelada           => 'nx-so-badge--cancelled',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Rascunho            => '#94A3B8',
            self::AguardandoAprovacao => '#F59E0B',
            self::Aprovada            => '#10B981',
            self::Rejeitada           => '#EF4444',
            self::Convertida          => '#059669',
            self::Cancelada           => '#EF4444',
        };
    }
}

