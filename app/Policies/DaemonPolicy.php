<?php

namespace App\Policies;

use App\Models\Daemon;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DaemonPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->currentTeam && $user->belongsToTeam($user->currentTeam);
    }

    public function view(User $user, Daemon $daemon): bool
    {
        return $daemon->team_id === $user->currentTeam?->id &&
               $user->belongsToTeam($daemon->team);
    }

    public function create(User $user): bool
    {
        return $user->currentTeam && $user->belongsToTeam($user->currentTeam);
    }

    public function update(User $user, Daemon $daemon): bool
    {
        return $daemon->team_id === $user->currentTeam?->id &&
               $user->belongsToTeam($daemon->team);
    }

    public function delete(User $user, Daemon $daemon): bool
    {
        return $daemon->team_id === $user->currentTeam?->id &&
               $user->belongsToTeam($daemon->team);
    }
}
