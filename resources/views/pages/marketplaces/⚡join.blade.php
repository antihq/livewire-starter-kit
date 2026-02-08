<?php

use App\Models\Marketplace;
use Illuminate\Support\Facades\Auth;
use Laravel\Jetstream\Events\AddingTeamMember;
use Laravel\Jetstream\Events\TeamMemberAdded;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.marketplace')] class extends Component
{
    public Marketplace $marketplace;

    #[Computed]
    public function user()
    {
        return Auth::user();
    }

    #[Computed]
    public function team()
    {
        return $this->marketplace->team;
    }

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

        return $this->redirectRoute('marketplaces.show', $this->marketplace);
    }
};
?>

<section class="mx-auto max-w-6xl space-y-8">
    <flux:heading size="xl">Join marketplace</flux:heading>

    <div class="space-y-6">
        <header class="space-y-1">
            <flux:heading size="lg">{{ $marketplace->name }}</flux:heading>
            <flux:text>You've been invited to join this marketplace.</flux:text>
        </header>

        @if ($this->team->hasUser($this->user))
            <flux:text variant="subtle">You are already a member of this marketplace.</flux:text>
            <flux:button href="{{ route('marketplaces.show', $marketplace) }}" variant="subtle">Go to marketplace</flux:button>
        @elseif ($this->user->ownsTeam($this->team))
            <flux:text variant="subtle">You cannot join your own marketplace.</flux:text>
            <flux:button href="{{ route('marketplaces.show', $marketplace) }}" variant="subtle">Go to marketplace</flux:button>
        @else
            <div class="space-y-4">
                @error('team')
                    <flux:badge variant="danger">{{ $message }}</flux:badge>
                @enderror

                <flux:button wire:click="join" variant="primary" class="w-full sm:w-auto">Join marketplace</flux:button>
            </div>
        @endif
    </div>
</section>
