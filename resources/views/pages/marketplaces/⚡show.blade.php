<?php

use App\Models\Marketplace;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public Marketplace $marketplace;

    public function mount()
    {
        $this->authorize('view', $this->marketplace);
    }

    #[Computed]
    public function listings()
    {
        return $this->marketplace->listings;
    }
};
?>

<section class="mx-auto max-w-lg">
    @if ($this->listings->count() === 0)
        <div class="flex flex-col items-center justify-center py-12">
            <flux:heading>No listings yet</flux:heading>
            <flux:text>Create your first listing for this marketplace.</flux:text>
            <div class="mt-6">
                <flux:button href="{{ route('marketplaces.listings.create', $marketplace) }}" variant="primary" wire:navigate>
                    Create listing
                </flux:button>
            </div>
        </div>
    @else
        <div class="flex flex-wrap justify-between gap-x-6 gap-y-4">
            <flux:heading size="xl">Listings</flux:heading>

            <flux:button href="{{ route('marketplaces.listings.create', $marketplace) }}" variant="primary" class="-my-0.5" wire:navigate>
                Create listing
            </flux:button>
        </div>

        <div class="mt-8">
            <hr role="presentation" class="w-full border-t border-zinc-950/10 dark:border-white/10" />
            <div class="divide-y divide-zinc-100 overflow-hidden dark:divide-white/5 dark:text-white">
                @foreach ($this->listings as $listing)
                    <div
                        wire:key="listing-{{ $listing->id }}"
                        class="relative flex items-center justify-between gap-4 py-4"
                    >
                        <div>
                            <flux:heading class="leading-6!">
                                <a href="{{ route('marketplaces.listings.show', [$marketplace, $listing]) }}" wire:navigate>
                                    {{ $listing->title }}
                                </a>
                            </flux:heading>
                        </div>
                        <div class="flex shrink-0 items-center gap-x-4">
                            <flux:dropdown align="end">
                                <flux:button variant="subtle" square icon="ellipsis-horizontal" />
                                <flux:menu>
                                    <flux:menu.item href="{{ route('marketplaces.listings.show', [$marketplace, $listing]) }}" wire:navigate>
                                        View
                                    </flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</section>
