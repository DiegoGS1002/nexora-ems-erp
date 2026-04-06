<?php

namespace App\Enums;

enum CategoriaVeiculo: string
{
    case Particular = 'particular';
    case Aluguel    = 'aluguel';
    case Oficial    = 'oficial';
    case Frota      = 'frota';

    public function label(): string
    {
        return match($this) {
            self::Particular => 'Particular',
            self::Aluguel    => 'Aluguel',
            self::Oficial    => 'Oficial',
            self::Frota      => 'Frota Corporativa',
        };
    }
}

