<?php

namespace App\Enums;

enum TipoPessoa: string
{
    case PF = 'PF';
    case PJ = 'PJ';

    public function label(): string
    {
        return match($this) {
            self::PF => 'Pessoa Física',
            self::PJ => 'Pessoa Jurídica',
        };
    }
}

