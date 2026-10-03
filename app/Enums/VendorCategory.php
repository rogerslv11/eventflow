<?php

namespace App\Enums;

enum VendorCategory: string
{
    case Buffet = 'buffet';
    case Security = 'security';
    case Photography = 'photography';
    case Sound = 'sound';
    case Lighting = 'lighting';
    case Decoration = 'decoration';
    case Transport = 'transport';
    case Cleaning = 'cleaning';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Buffet => 'Buffet',
            self::Security => 'Segurança',
            self::Photography => 'Fotografia',
            self::Sound => 'Som',
            self::Lighting => 'Iluminação',
            self::Decoration => 'Decoração',
            self::Transport => 'Transporte',
            self::Cleaning => 'Limpeza',
            self::Other => 'Outros',
        };
    }
}
