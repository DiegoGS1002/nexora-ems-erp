<?php

namespace App\Enums;

enum OrigemPedido: string
{
    case Manual = 'manual';
    case API = 'api';
    case Ecommerce = 'ecommerce';
    case Importacao = 'importacao';
    case ForcaVendas = 'forca_vendas';

    public function label(): string
    {
        return match($this) {
            self::Manual => 'Manual',
            self::API => 'API',
            self::Ecommerce => 'E-commerce',
            self::Importacao => 'Importação',
            self::ForcaVendas => 'Força de Vendas',
        };
    }
}

