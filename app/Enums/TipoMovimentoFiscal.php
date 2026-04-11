<?php

namespace App\Enums;

enum TipoMovimentoFiscal: string
{
    case Entrada = 'entrada';
    case Saida   = 'saida';

    public function label(): string
    {
        return match($this) {
            self::Entrada => 'Entrada',
            self::Saida   => 'Saída',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Entrada => 'nx-badge-info',
            self::Saida   => 'nx-badge-success',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Entrada => '↓',
            self::Saida   => '↑',
        };
    }
}

