<?php

use App\Models\Marketplace;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public $name = '';

    public function create()
    {
        $team = Auth::user()->currentTeam;

        $this->authorize('create', Marketplace::class);

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $mareketplace = $team->marketplaces()->create([
            'name' => $this->name,
        ]);

        return $this->redirectRoute('marketplaces.show', $marketplace);
    }

    #[Computed]
    public function user()
    {
        return Auth::user();
    }

    #[Computed]
    public function team()
    {
        return $this->user->currentTeam;
    }
};
?>

<section class="mx-auto max-w-lg space-y-8">
    <flux:heading size="xl">Create marketplace</flux:heading>

    <form wire:submit="create" class="mt-14 w-full max-w-lg space-y-8">
        <flux:input wire:model="name" label="Marketplace name" type="text" required autofocus />

        <div class="flex justify-end gap-4">
            <flux:button href="{{ route('marketplaces.index') }}" variant="ghost" wire:navigate>Cancel</flux:button>
            <flux:button variant="primary" type="submit">Add account</flux:button>
        </div>
    </form>
</section>
