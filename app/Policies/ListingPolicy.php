<?php

namespace App\Policies;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ListingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Listing $listing): bool
    {
        return $user->belongsToTeam($listing->marketplace->team);
    }

    public function create(User $user): bool
    {
        return true;
    }
}
