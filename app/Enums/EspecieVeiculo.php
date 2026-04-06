<?php

namespace App\Enums;

enum EspecieVeiculo: string
{
    case Passageiro = 'passageiro';
    case Carga      = 'carga';
    case Misto      = 'misto';
    case Especial   = 'especial';

    public function label(): string
    {
        return match($this) {
            self::Passageiro => 'Passageiro',
            self::Carga      => 'Carga',
            self::Misto      => 'Misto',
            self::Especial   => 'Especial',
        };
    }
}

