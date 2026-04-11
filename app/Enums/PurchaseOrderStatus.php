<?php

namespace App\Enums;

enum PurchaseOrderStatus: string
{
    case Rascunho            = 'rascunho';
    case AguardandoAprovacao = 'aguardando_aprovacao';
    case Aprovado            = 'aprovado';
    case EnviadoFornecedor   = 'enviado_fornecedor';
    case RecebidoParcial     = 'recebido_parcial';
    case RecebidoTotal       = 'recebido_total';
    case Cancelado           = 'cancelado';

    public function label(): string
    {
        return match($this) {
            self::Rascunho            => 'Rascunho',
            self::AguardandoAprovacao => 'Aguard. Aprovação',
            self::Aprovado            => 'Aprovado',
            self::EnviadoFornecedor   => 'Enviado',
            self::RecebidoParcial     => 'Rec. Parcial',
            self::RecebidoTotal       => 'Recebido',
            self::Cancelado           => 'Cancelado',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Rascunho            => 'nx-so-badge--draft',
            self::AguardandoAprovacao => 'nx-so-badge--picking',
            self::Aprovado            => 'nx-so-badge--approved',
            self::EnviadoFornecedor   => 'nx-so-badge--invoiced',
            self::RecebidoParcial     => 'nx-so-badge--aberto',
            self::RecebidoTotal       => 'nx-so-badge--delivered',
            self::Cancelado           => 'nx-so-badge--cancelled',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Rascunho            => '#94A3B8',
            self::AguardandoAprovacao => '#F59E0B',
            self::Aprovado            => '#10B981',
            self::EnviadoFornecedor   => '#6366F1',
            self::RecebidoParcial     => '#3B82F6',
            self::RecebidoTotal       => '#059669',
            self::Cancelado           => '#EF4444',
        };
    }
}

