<?php

namespace App\Enums;

enum RegimeTributario: string
{
    case SimplesNacional  = 'simples_nacional';
    case LucroPresumido   = 'lucro_presumido';
    case LucroReal        = 'lucro_real';
    case Todos            = 'todos';

    public function label(): string
    {
        return match($this) {
            self::SimplesNacional => 'Simples Nacional',
            self::LucroPresumido  => 'Lucro Presumido',
            self::LucroReal       => 'Lucro Real',
            self::Todos           => 'Todos os Regimes',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::SimplesNacional => 'nx-badge-success',
            self::LucroPresumido  => 'nx-badge-info',
            self::LucroReal       => 'nx-badge-warning',
            self::Todos           => 'nx-badge-gray',
        };
    }
}

