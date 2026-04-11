<?php

namespace App\Enums;

enum TipoOperacaoVenda: string
{
    case VendaNormal = 'venda_normal';
    case Bonificacao = 'bonificacao';
    case Consignacao = 'consignacao';
    case Transferencia = 'transferencia';

    public function label(): string
    {
        return match($this) {
            self::VendaNormal => 'Venda Normal',
            self::Bonificacao => 'Bonificação',
            self::Consignacao => 'Consignação',
            self::Transferencia => 'Transferência',
        };
    }
}

