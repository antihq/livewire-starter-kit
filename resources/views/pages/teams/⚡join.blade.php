<?php

use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Laravel\Jetstream\Events\AddingTeamMember;
use Laravel\Jetstream\Events\TeamMemberAdded;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public Team $team;

    public function join()
    {
        if ($this->team->hasUser($this->user)) {
            $this->addError('team', 'You are already a member of this team.');

            return;
        }

        if ($this->user->ownsTeam($this->team)) {
            $this->addError('team', 'You cannot join your own team.');

            return;
        }

        AddingTeamMember::dispatch($this->team, $this->user);

        $this->team->users()->attach($this->user, ['role' => 'member']);

        TeamMemberAdded::dispatch($this->team, $this->user);

        return $this->redirectRoute('dashboard');
    }

    #[Computed]
    public function user()
    {
        return Auth::user();
    }
};
?>

<section class="mx-auto max-w-6xl space-y-8">
    <flux:heading size="xl">Join team</flux:heading>

    <div class="space-y-6">
        <header class="space-y-1">
            <flux:heading size="lg">{{ $team->name }}</flux:heading>
            <flux:text>You've been invited to join this team.</flux:text>
        </header>

        @if ($team->hasUser($this->user))
            <flux:text variant="subtle">You are already a member of this team.</flux:text>
            <flux:button href="{{ route('dashboard') }}" variant="subtle">Go to dashboard</flux:button>
        @elseif ($this->user->ownsTeam($team))
            <flux:text variant="subtle">You cannot join your own team.</flux:text>
            <flux:button href="{{ route('dashboard') }}" variant="subtle">Go to dashboard</flux:button>
        @else
            <div class="space-y-4">
                @error('team')
                    <flux:badge variant="danger">{{ $message }}</flux:badge>
                @enderror

                <flux:button wire:click="join" variant="primary" class="w-full sm:w-auto">Join team</flux:button>
            </div>
        @endif
    </div>
</section>
