<?php

namespace App\Policies;

use App\Models\Server;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServerPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->currentTeam && $user->belongsToTeam($user->currentTeam);
    }

    public function view(User $user, Server $server): bool
    {
        return $server->team_id === $user->currentTeam?->id &&
               $user->belongsToTeam($server->team);
    }

    public function create(User $user): bool
    {
        return $user->currentTeam && $user->belongsToTeam($user->currentTeam);
    }

    public function delete(User $user, Server $server): bool
    {
        return $server->team_id === $user->currentTeam?->id &&
               $user->belongsToTeam($server->team);
    }
}
