<?php

namespace App\Enums;

enum StaffRole: string
{
    case Coordinator = 'coordinator';
    case Reception = 'reception';
    case CheckIn = 'check_in';
    case Security = 'security';
    case Production = 'production';
    case Photography = 'photography';
    case Support = 'support';
    case Finance = 'finance';

    public function label(): string
    {
        return match ($this) {
            self::Coordinator => 'Coordenador',
            self::Reception => 'Recepção',
            self::CheckIn => 'Check-in',
            self::Security => 'Segurança',
            self::Production => 'Produção',
            self::Photography => 'Fotografia',
            self::Support => 'Suporte',
            self::Finance => 'Financeiro',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Coordinator => 'primary',
            self::Reception => 'success',
            self::CheckIn => 'warning',
            self::Security => 'danger',
            self::Production => 'primary',
            self::Photography => 'primary',
            self::Support => 'neutral',
            self::Finance => 'success',
        };
    }
}
