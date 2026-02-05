<?php

namespace App\Policies;

use App\Models\Site;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SitePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->currentTeam && $user->belongsToTeam($user->currentTeam);
    }

    public function view(User $user, Site $site): bool
    {
        return $site->team_id === $user->currentTeam?->id &&
               $user->belongsToTeam($site->team);
    }

    public function create(User $user): bool
    {
        return $user->currentTeam && $user->belongsToTeam($user->currentTeam);
    }

    public function delete(User $user, Site $site): bool
    {
        return $site->team_id === $user->currentTeam?->id &&
               $user->belongsToTeam($site->team);
    }
}
