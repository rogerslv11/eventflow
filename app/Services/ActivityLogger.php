<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    public static function log(
        string $action,
        ?Model $entity = null,
        ?string $description = null,
        array $properties = [],
        array $changes = [],
    ): ActivityLog {
        $user = Auth::user();

        return ActivityLog::create([
            'organization_id' => $user?->organizations()->first()?->id,
            'user_id' => $user?->id,
            'action' => $action,
            'entity_type' => $entity ? get_class($entity) : null,
            'entity_id' => $entity?->id,
            'description' => $description,
            'properties' => $properties,
            'changes' => $changes,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'performed_at' => now(),
        ]);
    }

    public static function login(User $user): void
    {
        ActivityLog::create([
            'user_id' => $user->id,
            'organization_id' => $user->organizations()->first()?->id,
            'action' => 'login',
            'description' => "Login realizado por {$user->name}",
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'performed_at' => now(),
        ]);
    }

    public static function logout(User $user): void
    {
        ActivityLog::create([
            'user_id' => $user->id,
            'organization_id' => $user->organizations()->first()?->id,
            'action' => 'logout',
            'description' => "Logout realizado por {$user->name}",
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'performed_at' => now(),
        ]);
    }
}
