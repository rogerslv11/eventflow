<?php

namespace App\Enums;

enum TicketStatus: string
{
    case Active = 'active';
    case Used = 'used';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Ativo',
            self::Used => 'Utilizado',
            self::Cancelled => 'Cancelado',
            self::Refunded => 'Reembolsado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'primary',
            self::Used => 'success',
            self::Cancelled => 'danger',
            self::Refunded => 'neutral',
        };
    }
}
