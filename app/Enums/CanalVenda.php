<?php

namespace App\Enums;

enum CanalVenda: string
{
    case Balcao = 'balcao';
    case Online = 'online';
    case Representante = 'representante';
    case Televendas = 'televendas';
    case Mobile = 'mobile';

    public function label(): string
    {
        return match($this) {
            self::Balcao => 'Balcão',
            self::Online => 'Online',
            self::Representante => 'Representante',
            self::Televendas => 'Televendas',
            self::Mobile => 'Mobile',
        };
    }
}

