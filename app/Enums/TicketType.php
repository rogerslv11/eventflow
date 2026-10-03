<?php

namespace App\Enums;

enum TicketType: string
{
    case Free = 'free';
    case Paid = 'paid';
    case Vip = 'vip';
    case Premium = 'premium';
    case Courtesy = 'courtesy';
    case Press = 'press';

    public function label(): string
    {
        return match ($this) {
            self::Free => 'Gratuito',
            self::Paid => 'Pago',
            self::Vip => 'VIP',
            self::Premium => 'Premium',
            self::Courtesy => 'Cortesia',
            self::Press => 'Imprensa',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Free => 'neutral',
            self::Paid => 'primary',
            self::Vip => 'warning',
            self::Premium => 'success',
            self::Courtesy => 'neutral',
            self::Press => 'primary',
        };
    }
}
