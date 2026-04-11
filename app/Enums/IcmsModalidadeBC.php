<?php

namespace App\Enums;

enum IcmsModalidadeBC: int
{
    case TransacaoMargem = 0;
    case Pauta           = 1;
    case PrecoMaximo     = 2;
    case ValorOperacao   = 3;

    public function label(): string
    {
        return match($this) {
            self::TransacaoMargem => '0 – Margem de Valor Agregado',
            self::Pauta           => '1 – Pauta (Valor)',
            self::PrecoMaximo     => '2 – Preço Tabelado Máx.',
            self::ValorOperacao   => '3 – Valor da Operação',
        };
    }
}

