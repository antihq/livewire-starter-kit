<?php

namespace App\Policies;

use App\Models\Marketplace;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MarketplacePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Marketplace $marketplace): bool
    {
        return $user->belongsToTeam($marketplace->team);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Marketplace $marketplace): bool
    {
        return $user->ownsTeam($marketplace->team);
    }

    public function delete(User $user, Marketplace $marketplace): bool
    {
        return $user->ownsTeam($marketplace->team);
    }
}
