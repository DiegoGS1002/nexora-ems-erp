<?php

namespace App\Enums;

enum CotacaoStatus: string
{
    case Rascunho   = 'rascunho';
    case Aberta     = 'aberta';
    case Aguardando = 'aguardando';
    case Respondida = 'respondida';
    case Aprovada   = 'aprovada';
    case Convertida = 'convertida';
    case Cancelada  = 'cancelada';

    public function label(): string
    {
        return match($this) {
            self::Rascunho   => 'Rascunho',
            self::Aberta     => 'Aberta',
            self::Aguardando => 'Aguardando',
            self::Respondida => 'Respondida',
            self::Aprovada   => 'Aprovada',
            self::Convertida => 'Convertida',
            self::Cancelada  => 'Cancelada',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Rascunho   => 'nx-so-badge--draft',
            self::Aberta     => 'nx-so-badge--aberto',
            self::Aguardando => 'nx-so-badge--picking',
            self::Respondida => 'nx-so-badge--invoiced',
            self::Aprovada   => 'nx-so-badge--approved',
            self::Convertida => 'nx-so-badge--delivered',
            self::Cancelada  => 'nx-so-badge--cancelled',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Rascunho   => '#94A3B8',
            self::Aberta     => '#3B82F6',
            self::Aguardando => '#F59E0B',
            self::Respondida => '#6366F1',
            self::Aprovada   => '#10B981',
            self::Convertida => '#059669',
            self::Cancelada  => '#EF4444',
        };
    }
}

