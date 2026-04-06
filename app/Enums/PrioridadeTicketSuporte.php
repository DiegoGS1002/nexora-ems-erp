<?php

namespace App\Enums;

enum PrioridadeTicketSuporte: string
{
    case Baixa   = 'baixa';
    case Media   = 'media';
    case Alta    = 'alta';
    case Urgente = 'urgente';

    public function label(): string
    {
        return match($this) {
            self::Baixa   => 'Baixa',
            self::Media   => 'Média',
            self::Alta    => 'Alta',
            self::Urgente => 'Urgente',
        };
    }

    public function cssClass(): string
    {
        return match($this) {
            self::Baixa   => 'nx-chat-prio--gray',
            self::Media   => 'nx-chat-prio--yellow',
            self::Alta    => 'nx-chat-prio--orange',
            self::Urgente => 'nx-chat-prio--red',
        };
    }
}

