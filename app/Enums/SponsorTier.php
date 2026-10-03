<?php

namespace App\Enums;

enum SponsorTier: string
{
    case Master = 'master';
    case Gold = 'gold';
    case Silver = 'silver';
    case Bronze = 'bronze';
    case Support = 'support';

    public function label(): string
    {
        return match ($this) {
            self::Master => 'Master',
            self::Gold => 'Ouro',
            self::Silver => 'Prata',
            self::Bronze => 'Bronze',
            self::Support => 'Apoio',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Master => 'warning',
            self::Gold => 'warning',
            self::Silver => 'neutral',
            self::Bronze => 'warning',
            self::Support => 'neutral',
        };
    }

    public function sortOrder(): int
    {
        return match ($this) {
            self::Master => 1,
            self::Gold => 2,
            self::Silver => 3,
            self::Bronze => 4,
            self::Support => 5,
        };
    }
}
