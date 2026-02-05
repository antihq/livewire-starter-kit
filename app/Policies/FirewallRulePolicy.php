<?php

namespace App\Policies;

use App\Models\FirewallRule;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FirewallRulePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->currentTeam && $user->belongsToTeam($user->currentTeam);
    }

    public function view(User $user, FirewallRule $firewallRule): bool
    {
        return $firewallRule->team_id === $user->currentTeam?->id &&
               $user->belongsToTeam($firewallRule->team);
    }

    public function create(User $user): bool
    {
        return $user->currentTeam && $user->belongsToTeam($user->currentTeam);
    }

    public function update(User $user, FirewallRule $firewallRule): bool
    {
        return $firewallRule->team_id === $user->currentTeam?->id &&
               $user->belongsToTeam($firewallRule->team);
    }

    public function delete(User $user, FirewallRule $firewallRule): bool
    {
        return $firewallRule->team_id === $user->currentTeam?->id &&
               $user->belongsToTeam($firewallRule->team);
    }
}
