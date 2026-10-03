<?php

namespace App\Enums;

enum CheckInStatus: string
{
    case Success = 'success';
    case Duplicate = 'duplicate';
    case Invalid = 'invalid';
    case Expired = 'expired';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Success => 'Confirmado',
            self::Duplicate => 'Já realizado',
            self::Invalid => 'Inválido',
            self::Expired => 'Expirado',
            self::Cancelled => 'Cancelado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Success => 'success',
            self::Duplicate => 'warning',
            self::Invalid => 'danger',
            self::Expired => 'neutral',
            self::Cancelled => 'neutral',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Success => 'check-circle',
            self::Duplicate => 'alert-triangle',
            self::Invalid => 'x-circle',
            self::Expired => 'clock',
            self::Cancelled => 'ban',
        };
    }
}
