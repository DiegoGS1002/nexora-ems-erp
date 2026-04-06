<?php

namespace App\Enums;

enum TipoProduto: string
{
    case Fisico  = 'produto_fisico';
    case Servico = 'servico';

    public function label(): string
    {
        return match($this) {
            self::Fisico  => 'Produto Físico',
            self::Servico => 'Serviço',
        };
    }
}

