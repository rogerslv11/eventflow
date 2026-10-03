<?php

namespace App\Enums;

enum OrganizationRole: string
{
    case SuperAdmin = 'super_admin';
    case Owner = 'owner';
    case Admin = 'admin';
    case Manager = 'manager';
    case Staff = 'staff';
    case CheckInStaff = 'check_in_staff';
    case Finance = 'finance';
    case Viewer = 'viewer';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Owner => 'Proprietário',
            self::Admin => 'Administrador',
            self::Manager => 'Gerente de Eventos',
            self::Staff => 'Equipe',
            self::CheckInStaff => 'Check-in',
            self::Finance => 'Financeiro',
            self::Viewer => 'Visualizador',
        };
    }

    public function permissions(): array
    {
        return match ($this) {
            self::SuperAdmin => ['*'],
            self::Owner => ['*'],
            self::Admin => [
                'events.*', 'tickets.*', 'participants.*', 'check_in.*',
                'team.*', 'vendors.*', 'sponsors.*', 'coupons.*',
                'reports.view', 'settings.*',
            ],
            self::Manager => [
                'events.view', 'events.create', 'events.edit',
                'tickets.*', 'participants.*', 'check_in.*',
                'team.view', 'team.edit', 'coupons.*',
                'reports.view',
            ],
            self::Staff => [
                'events.view', 'participants.view', 'team.view',
            ],
            self::CheckInStaff => [
                'events.view', 'check_in.*', 'participants.view',
            ],
            self::Finance => [
                'events.view', 'orders.*', 'reports.view', 'coupons.*',
            ],
            self::Viewer => [
                'events.view', 'participants.view', 'reports.view',
            ],
        };
    }
}
