<?php

namespace App\Enums;

enum FiscalNoteEventType: string
{
    case Authorized = 'authorized';
    case Rejected = 'rejected';
    case Denied = 'denied';
    case Cancelled = 'cancelled';
    case Cce = 'cce';

    public function label(): string
    {
        return match ($this) {
            self::Authorized => 'Autorização',
            self::Rejected => 'Rejeição',
            self::Denied => 'Denegação',
            self::Cancelled => 'Cancelamento',
            self::Cce => 'Carta de Correção',
        };
    }
}
