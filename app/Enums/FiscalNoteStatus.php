<?php

namespace App\Enums;

enum FiscalNoteStatus: string
{
    case Draft      = 'draft';
    case Sent       = 'sent';
    case Authorized = 'authorized';
    case Rejected   = 'rejected';
    case Cancelled  = 'cancelled';
    case Denied     = 'denied';

    public function label(): string
    {
        return match($this) {
            self::Draft      => 'Rascunho',
            self::Sent       => 'Enviada',
            self::Authorized => 'Autorizada',
            self::Rejected   => 'Rejeitada',
            self::Cancelled  => 'Cancelada',
            self::Denied     => 'Denegada',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Draft      => 'nx-badge-neutral',
            self::Sent       => 'nx-badge-info',
            self::Authorized => 'nx-badge-success',
            self::Rejected   => 'nx-badge-danger',
            self::Cancelled  => 'nx-badge-warning',
            self::Denied     => 'nx-badge-danger',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Draft      => '#64748B',
            self::Sent       => '#1D4ED8',
            self::Authorized => '#15803D',
            self::Rejected   => '#B91C1C',
            self::Cancelled  => '#D97706',
            self::Denied     => '#7C2D12',
        };
    }

    public function dotClass(): string
    {
        return match($this) {
            self::Draft      => 'nx-dot-nfe-draft',
            self::Sent       => 'nx-dot-nfe-sent',
            self::Authorized => 'nx-dot-nfe-authorized',
            self::Rejected   => 'nx-dot-nfe-rejected',
            self::Cancelled  => 'nx-dot-nfe-cancelled',
            self::Denied     => 'nx-dot-nfe-denied',
        };
    }
}

