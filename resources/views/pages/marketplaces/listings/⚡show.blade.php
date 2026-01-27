<?php

use App\Models\Listing;
use App\Models\Marketplace;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public Marketplace $marketplace;

    public Listing $listing;

    public function mount()
    {
        $this->authorize('view', $this->marketplace);
        $this->authorize('view', $this->listing);
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
}
?>

<section class="mx-auto max-w-6xl space-y-8">
    <div class="flex items-center gap-4">
        <flux:button href="{{ route('marketplaces.show', $marketplace) }}" wire:navigate variant="ghost">
            Back
        </flux:button>
        <flux:heading size="xl">{{ $listing->title }}</flux:heading>
    </div>

    <div class="space-y-6">
        <div class="space-y-1">
            <flux:heading size="lg">Description</flux:heading>
            <flux:text>{{ $listing->description }}</flux:text>
        </div>

        @if ($this->user)
            @if ($this->user->id === $listing->user_id)
                <flux:button href="{{ route('marketplaces.listings.conversation', [$marketplace, $listing]) }}" wire:navigate>
                    View messages
                </flux:button>
            @else
                <flux:button href="{{ route('marketplaces.listings.message', [$marketplace, $listing]) }}" wire:navigate>
                    Send message
                </flux:button>
            @endif
        @endif
    </div>
</section>
