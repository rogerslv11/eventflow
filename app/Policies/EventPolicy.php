<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user, ?Organization $organization = null): bool
    {
        if ($organization) {
            return $this->userBelongsToOrganization($user, $organization);
        }

        return true;
    }

    public function view(User $user, Event $event): bool
    {
        if ($event->is_public) {
            return true;
        }

        return $this->userBelongsToOrganization($user, $event->organization);
    }

    public function create(User $user, Organization $organization): bool
    {
        return $this->userHasRole($user, $organization, ['owner', 'admin', 'manager']);
    }

    public function update(User $user, Event $event): bool
    {
        return $this->userHasRole($user, $event->organization, ['owner', 'admin', 'manager']);
    }

    public function delete(User $user, Event $event): bool
    {
        return $this->userHasRole($user, $event->organization, ['owner', 'admin']);
    }

    public function duplicate(User $user, Event $event): bool
    {
        return $this->userHasRole($user, $event->organization, ['owner', 'admin', 'manager']);
    }

    public function publish(User $user, Event $event): bool
    {
        return $this->userHasRole($user, $event->organization, ['owner', 'admin', 'manager']);
    }

    private function userBelongsToOrganization(User $user, Organization $organization): bool
    {
        return $user->organizations()->where('organizations.id', $organization->id)->exists();
    }

    private function userHasRole(User $user, Organization $organization, array $roles): bool
    {
        $member = $user->organizations()->where('organizations.id', $organization->id)->first();
        if (! $member) {
            return false;
        }

        return in_array($member->pivot->role, $roles);
    }
}
