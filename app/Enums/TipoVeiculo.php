<?php

namespace App\Enums;

enum TipoVeiculo: string
{
    case Utilitario = 'utilitario';
    case Caminhao   = 'caminhao';
    case Passeio    = 'passeio';
    case Moto       = 'moto';
    case Onibus     = 'onibus';
    case Minivan    = 'minivan';
    case Pickupe    = 'pickupe';
    case VanFurgao  = 'van_furgao';

    public function label(): string
    {
        return match($this) {
            self::Utilitario => 'Utilitário',
            self::Caminhao   => 'Caminhão',
            self::Passeio    => 'Passeio',
            self::Moto       => 'Moto',
            self::Onibus     => 'Ônibus',
            self::Minivan    => 'Minivan',
            self::Pickupe    => 'Pickup/SUV',
            self::VanFurgao  => 'Van / Furgão',
        };
    }
}

