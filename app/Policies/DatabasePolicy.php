<?php

namespace App\Policies;

use App\Models\Database;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DatabasePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->currentTeam && $user->belongsToTeam($user->currentTeam);
    }

    public function view(User $user, Database $database): bool
    {
        return $database->team_id === $user->currentTeam?->id &&
               $user->belongsToTeam($database->team);
    }

    public function create(User $user): bool
    {
        return $user->currentTeam && $user->belongsToTeam($user->currentTeam);
    }

    public function delete(User $user, Database $database): bool
    {
        return $database->team_id === $user->currentTeam?->id &&
               $user->belongsToTeam($database->team);
    }
}
