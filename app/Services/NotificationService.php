<?php

namespace App\Services;

use App\Models\Notification as NotificationModel;
use App\Models\User;

class NotificationService
{
    public static function send(
        User $user,
        string $type,
        string $title,
        string $message,
        string $icon = 'bell',
        string $color = 'primary',
        ?string $actionUrl = null,
        array $data = [],
        $entity = null,
    ): NotificationModel {
        return NotificationModel::create([
            'user_id' => $user->id,
            'organization_id' => $user->organizations()->first()?->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
            'color' => $color,
            'action_url' => $actionUrl,
            'data' => $data,
        ]);
    }

    public static function notifyAdmins(
        string $type,
        string $title,
        string $message,
        ?int $organizationId = null,
        string $icon = 'bell',
        string $color = 'primary',
        ?string $actionUrl = null,
    ): void {
        $query = User::query()->where('is_active', true);

        if ($organizationId) {
            $query->whereHas('organizations', fn ($q) => $q->where('organizations.id', $organizationId));
        }

        $users = $query->get();

        foreach ($users as $user) {
            self::send($user, $type, $title, $message, $icon, $color, $actionUrl);
        }
    }
}
