<?php

namespace App\Policies;

use App\Models\CheckIn;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CheckInPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function perform(User $user, Ticket $ticket): bool
    {
        if (! $ticket->event->is_public && $ticket->event->organization) {
            $member = $user->organizations()
                ->where('organizations.id', $ticket->event->organization_id)
                ->first();

            if (! $member) {
                return false;
            }

            $allowedRoles = ['owner', 'admin', 'manager', 'staff', 'check_in_staff'];

            return in_array($member->pivot->role, $allowedRoles);
        }

        return false;
    }

    public function view(User $user, CheckIn $checkIn): bool
    {
        if (! $checkIn->event->organization_id) {
            return false;
        }

        return $user->organizations()
            ->where('organizations.id', $checkIn->event->organization_id)
            ->exists();
    }
}
