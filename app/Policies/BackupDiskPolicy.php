<?php

namespace App\Policies;

use App\Models\BackupDisk;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BackupDiskPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->currentTeam && $user->belongsToTeam($user->currentTeam);
    }

    public function create(User $user): bool
    {
        return $user->currentTeam && $user->belongsToTeam($user->currentTeam);
    }

    public function update(User $user, BackupDisk $backupDisk): bool
    {
        return $backupDisk->team_id === $user->currentTeam?->id &&
               $user->belongsToTeam($backupDisk->team);
    }

    public function delete(User $user, BackupDisk $backupDisk): bool
    {
        return $backupDisk->team_id === $user->currentTeam?->id &&
               $user->belongsToTeam($backupDisk->team);
    }
}
