<?php

use App\Models\Listing;
use App\Models\Marketplace;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.marketplace')] class extends Component
{
    public Marketplace $marketplace;

    public $title = '';

    public $description = '';

    public function mount()
    {
        $this->authorize('view', $this->marketplace);
    }

    public function create()
    {
        $this->authorize('create', Listing::class);

        $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ]);

        $listing = $this->marketplace->listings()->create([
            'title' => $this->title,
            'description' => $this->description,
            'creator_id' => Auth::id(),
            'team_id' => $this->team->id,
        ]);

        return $this->redirectRoute('listings.show', $listing);
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

<section class="mx-auto max-w-lg">
    <flux:heading size="xl">New listing</flux:heading>

    <div class="mt-14 space-y-14">
        <form wire:submit="create" class="w-full max-w-lg space-y-8">
            <flux:input wire:model="title" label="Listing title" type="text" required autofocus />
            <flux:textarea wire:model="description" label="Description" required />
            <div class="flex justify-end gap-4">
                <flux:button href="{{ route('marketplaces.show', $marketplace) }}" variant="ghost" wire:navigate>Cancel</flux:button>
                <flux:button variant="primary" type="submit">Publish listing</flux:button>
            </div>
        </form>
    </div>
</section>
