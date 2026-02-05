<?php

namespace App\Policies;

use App\Models\Cronjob;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CronjobPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->currentTeam && $user->belongsToTeam($user->currentTeam);
    }

    public function view(User $user, Cronjob $cronjob): bool
    {
        return $cronjob->team_id === $user->currentTeam?->id &&
               $user->belongsToTeam($cronjob->team);
    }

    public function create(User $user): bool
    {
        return $user->currentTeam && $user->belongsToTeam($user->currentTeam);
    }

    public function update(User $user, Cronjob $cronjob): bool
    {
        return $cronjob->team_id === $user->currentTeam?->id &&
               $user->belongsToTeam($cronjob->team);
    }

    public function delete(User $user, Cronjob $cronjob): bool
    {
        return $cronjob->team_id === $user->currentTeam?->id &&
               $user->belongsToTeam($cronjob->team);
    }
}
