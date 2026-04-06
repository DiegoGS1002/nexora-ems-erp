<?php

namespace App\Enums;

enum CombustivelVeiculo: string
{
    case Flex      = 'flex';
    case Gasolina  = 'gasolina';
    case Etanol    = 'etanol';
    case Diesel    = 'diesel';
    case Eletrico  = 'eletrico';
    case Hibrido   = 'hibrido';
    case Gnv       = 'gnv';

    public function label(): string
    {
        return match($this) {
            self::Flex     => 'Flex (Gasolina/Etanol)',
            self::Gasolina => 'Gasolina',
            self::Etanol   => 'Etanol',
            self::Diesel   => 'Diesel',
            self::Eletrico => 'Elétrico',
            self::Hibrido  => 'Híbrido',
            self::Gnv      => 'GNV',
        };
    }
}

