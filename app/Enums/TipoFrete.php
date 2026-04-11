<?php

namespace App\Enums;

enum TipoFrete: string
{
    case CIF = 'cif'; // Por conta do remetente
    case FOB = 'fob'; // Por conta do destinatário
    case SemFrete = 'sem_frete';
    case Terceiros = 'terceiros';

    public function label(): string
    {
        return match($this) {
            self::CIF => 'CIF (Por conta do Remetente)',
            self::FOB => 'FOB (Por conta do Destinatário)',
            self::SemFrete => 'Sem Frete',
            self::Terceiros => 'Terceiros',
        };
    }
}

