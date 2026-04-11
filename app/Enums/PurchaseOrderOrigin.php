<?php

namespace App\Enums;

enum PurchaseOrderOrigin: string
{
    case Manual      = 'manual';
    case Cotacao     = 'cotacao';
    case Solicitacao = 'solicitacao';

    public function label(): string
    {
        return match($this) {
            self::Manual      => 'Manual',
            self::Cotacao     => 'Cotação',
            self::Solicitacao => 'Solicitação',
        };
    }
}

