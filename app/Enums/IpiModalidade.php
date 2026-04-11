<?php

namespace App\Enums;

enum IpiModalidade: string
{
    case Aliquota = 'aliquota';
    case Pauta    = 'pauta';
    case Unidade  = 'unidade';
    case Isento   = 'isento';

    public function label(): string
    {
        return match($this) {
            self::Aliquota => 'Alíquota (%)',
            self::Pauta    => 'Pauta (valor fixo)',
            self::Unidade  => 'Por Unidade',
            self::Isento   => 'Isento / Não Tributado',
        };
    }
}

