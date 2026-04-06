<?php

namespace App\Enums;

enum StatusTicketSuporte: string
{
    case Aberto     = 'aberto';
    case EmAndamento = 'em_andamento';
    case Resolvido  = 'resolvido';
    case Fechado    = 'fechado';

    public function label(): string
    {
        return match($this) {
            self::Aberto      => 'Aberto',
            self::EmAndamento => 'Em Andamento',
            self::Resolvido   => 'Resolvido',
            self::Fechado     => 'Fechado',
        };
    }

    public function cssClass(): string
    {
        return match($this) {
            self::Aberto      => 'nx-chat-badge--green',
            self::EmAndamento => 'nx-chat-badge--blue',
            self::Resolvido   => 'nx-chat-badge--purple',
            self::Fechado     => 'nx-chat-badge--gray',
        };
    }
}

