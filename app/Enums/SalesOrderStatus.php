<?php

namespace App\Enums;

enum SalesOrderStatus: string
{
    case Draft           = 'draft';
    case Aberto          = 'aberto';
    case AguardandoConf  = 'aguardando_conferencia';
    case ProntoFaturar   = 'pronto_faturar';
    case Approved        = 'approved';
    case EmSeparacao     = 'em_separacao';
    case Invoiced        = 'invoiced';
    case NfTransmitida   = 'nf_transmitida';
    case NfRejeitada     = 'nf_rejeitada';
    case Delivered       = 'delivered';
    case Cancelled       = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Draft          => 'Rascunho',
            self::Aberto         => 'Aberto',
            self::AguardandoConf => 'Aguard. Conferência',
            self::ProntoFaturar  => 'Pronto p/ Faturar',
            self::Approved       => 'Aprovado',
            self::EmSeparacao    => 'Em Separação',
            self::Invoiced       => 'Faturado',
            self::NfTransmitida  => 'NF Transmitida',
            self::NfRejeitada    => 'NF Rejeitada',
            self::Delivered      => 'Entregue',
            self::Cancelled      => 'Cancelado',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Draft          => 'nx-so-badge--draft',
            self::Aberto         => 'nx-so-badge--open',
            self::AguardandoConf => 'nx-so-badge--waiting',
            self::ProntoFaturar  => 'nx-so-badge--ready',
            self::Approved       => 'nx-so-badge--approved',
            self::EmSeparacao    => 'nx-so-badge--picking',
            self::Invoiced       => 'nx-so-badge--invoiced',
            self::NfTransmitida  => 'nx-so-badge--transmitted',
            self::NfRejeitada    => 'nx-so-badge--rejected',
            self::Delivered      => 'nx-so-badge--delivered',
            self::Cancelled      => 'nx-so-badge--cancelled',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Draft          => '#94A3B8',
            self::Aberto         => '#06B6D4',
            self::AguardandoConf => '#A78BFA',
            self::ProntoFaturar  => '#60A5FA',
            self::Approved       => '#3B82F6',
            self::EmSeparacao    => '#F59E0B',
            self::Invoiced       => '#8B5CF6',
            self::NfTransmitida  => '#10B981',
            self::NfRejeitada    => '#EF4444',
            self::Delivered      => '#059669',
            self::Cancelled      => '#EF4444',
        };
    }

    public function isEditable(): bool
    {
        return in_array($this, [self::Draft, self::Aberto, self::NfRejeitada]);
    }

    public function canInvoice(): bool
    {
        return in_array($this, [self::Approved, self::ProntoFaturar, self::Aberto]);
    }
}
