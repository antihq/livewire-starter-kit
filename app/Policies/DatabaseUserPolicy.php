<?php

namespace App\Policies;

use App\Models\DatabaseUser;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DatabaseUserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->currentTeam && $user->belongsToTeam($user->currentTeam);
    }

    public function view(User $user, DatabaseUser $databaseUser): bool
    {
        return $databaseUser->team_id === $user->currentTeam?->id &&
               $user->belongsToTeam($databaseUser->team);
    }

    public function create(User $user): bool
    {
        return $user->currentTeam && $user->belongsToTeam($user->currentTeam);
    }

    public function delete(User $user, DatabaseUser $databaseUser): bool
    {
        return $databaseUser->team_id === $user->currentTeam?->id &&
               $user->belongsToTeam($databaseUser->team);
    }
}
