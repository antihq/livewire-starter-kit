<?php

namespace App\Policies;

use App\Models\SshKey;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SshKeyPolicy
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

    public function delete(User $user, SshKey $sshKey): bool
    {
        return $sshKey->team_id === $user->currentTeam?->id &&
               $user->belongsToTeam($sshKey->team);
    }
}
