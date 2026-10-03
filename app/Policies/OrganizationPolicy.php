<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrganizationPolicy
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

    public function view(User $user, Organization $organization): bool
    {
        return $user->organizations()->where('organizations.id', $organization->id)->exists();
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Organization $organization): bool
    {
        return $this->userHasRole($user, $organization, ['owner', 'admin']);
    }

    public function delete(User $user, Organization $organization): bool
    {
        return $this->userHasRole($user, $organization, ['owner']);
    }

    public function manageMembers(User $user, Organization $organization): bool
    {
        return $this->userHasRole($user, $organization, ['owner', 'admin']);
    }

    public function manageBilling(User $user, Organization $organization): bool
    {
        return $this->userHasRole($user, $organization, ['owner']);
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
