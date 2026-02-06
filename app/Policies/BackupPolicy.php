<?php

namespace App\Policies;

use App\Models\Backup;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BackupPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->currentTeam && $user->belongsToTeam($user->currentTeam);
    }

    public function view(User $user, Backup $backup): bool
    {
        return $backup->team_id === $user->currentTeam?->id &&
               $user->belongsToTeam($backup->team);
    }

    public function create(User $user): bool
    {
        return $user->currentTeam && $user->belongsToTeam($user->currentTeam);
    }

    public function update(User $user, Backup $backup): bool
    {
        return $backup->team_id === $user->currentTeam?->id &&
               $user->belongsToTeam($backup->team);
    }

    public function delete(User $user, Backup $backup): bool
    {
        return $backup->team_id === $user->currentTeam?->id &&
               $user->belongsToTeam($backup->team);
    }
}
