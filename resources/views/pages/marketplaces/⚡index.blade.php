<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
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

    #[Computed]
    public function marketplaces()
    {
        return $this->team->marketplaces;
    }
};
?>

<section class="mx-auto max-w-lg">
    <div class="flex flex-wrap justify-between gap-x-6 gap-y-4">
        <flux:heading size="xl">Marketplaces</flux:heading>

        <flux:button href="{{ route('marketplaces.create') }}" variant="primary" class="-my-0.5" wire:navigate>
            Create marketplace
        </flux:button>
    </div>

    <div class="mt-8">
        <hr role="presentation" class="w-full border-t border-zinc-950/10 dark:border-white/10" />
        <div class="divide-y divide-zinc-100 overflow-hidden dark:divide-white/5 dark:text-white">
            @foreach ($this->marketplaces as $marketplace)
                <div
                    wire:key="marketplace-{{ $marketplace->id }}"
                    class="relative flex items-center justify-between gap-4 py-4"
                >
                    <div>
                        <flux:heading class="leading-6!">
                            <a href="{{ route('marketplaces.show', $marketplace) }}" wire:navigate>
                                {{ $marketplace->name }}
                            </a>
                        </flux:heading>
                    </div>
                    <div class="flex shrink-0 items-center gap-x-4">
                        <flux:dropdown align="end">
                            <flux:button variant="subtle" square icon="ellipsis-horizontal" />
                            <flux:menu>
                                <flux:menu.item href="{{ route('marketplaces.show', $marketplace) }}" wire:navigate>
                                    View
                                </flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
