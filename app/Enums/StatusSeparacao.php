<?php

namespace App\Enums;

enum StatusSeparacao: string
{
    case AguardandoSeparacao = 'aguardando_separacao';
    case EmSeparacao = 'em_separacao';
    case Separado = 'separado';
    case Conferido = 'conferido';
    case Expedido = 'expedido';

    public function label(): string
    {
        return match($this) {
            self::AguardandoSeparacao => 'Aguardando Separação',
            self::EmSeparacao => 'Em Separação',
            self::Separado => 'Separado',
            self::Conferido => 'Conferido',
            self::Expedido => 'Expedido',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::AguardandoSeparacao => '#94A3B8',
            self::EmSeparacao => '#F59E0B',
            self::Separado => '#3B82F6',
            self::Conferido => '#10B981',
            self::Expedido => '#059669',
        };
    }
}

