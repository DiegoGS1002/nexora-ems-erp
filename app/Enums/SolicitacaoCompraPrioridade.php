<?php

namespace App\Enums;

enum SolicitacaoCompraPrioridade: string
{
    case Baixa   = 'baixa';
    case Normal  = 'normal';
    case Alta    = 'alta';
    case Urgente = 'urgente';

    public function label(): string
    {
        return match($this) {
            self::Baixa   => 'Baixa',
            self::Normal  => 'Normal',
            self::Alta    => 'Alta',
            self::Urgente => 'Urgente',
        };
    }

    public function badgeStyle(): string
    {
        return match($this) {
            self::Baixa   => 'background:#F1F5F9;color:#64748B;',
            self::Normal  => 'background:#DBEAFE;color:#1D4ED8;',
            self::Alta    => 'background:#FEF3C7;color:#D97706;',
            self::Urgente => 'background:#FEE2E2;color:#DC2626;',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Baixa   => '#94A3B8',
            self::Normal  => '#3B82F6',
            self::Alta    => '#F59E0B',
            self::Urgente => '#EF4444',
        };
    }
}

