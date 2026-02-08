<?php

use App\Models\Listing;
use App\Models\Marketplace;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.marketplace')] class extends Component
{
    public Listing $listing;

    public function mount()
    {
        $this->authorize('view', $this->listing);

        request()->merge([
            'marketplace' => $this->listing->marketplace,
        ]);
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

    #[Computed]
    public function creator()
    {
        return $this->listing->creator;
    }
}
?>

<section class="mx-auto max-w-lg">
    <div class="mt-4 lg:mt-8">
        <div class="flex items-center gap-4">
            <h1 class="text-2xl/8 font-semibold text-zinc-950 sm:text-xl/8 dark:text-white">{{ $listing->title }}</h1>
        </div>

        <div class="isolate mt-2.5 flex flex-wrap justify-between gap-x-6 gap-y-4">
            <div class="flex flex-wrap gap-x-10 gap-y-4 py-1.5">
                <span class="flex items-center gap-3 text-base/6 text-zinc-950 sm:text-sm/6 dark:text-white">
                    <x-boring-avatar
                        :name="$this->creator->name ?? $this->creator->email"
                        variant="beam"
                        size="16"
                        class="size-4 shrink-0"
                    />
                    <span>{{ $this->creator->name ?? $this->creator->email }}</span>
                </span>

                <span class="flex items-center gap-3 text-base/6 text-zinc-950 sm:text-sm/6 dark:text-white">
                    <flux:icon name="calendar" variant="micro" class="size-4 shrink-0 fill-zinc-400 dark:fill-zinc-500" />
                    <span>{{ $listing->created_at->format('M j, Y') }}</span>
                </span>
            </div>

            <div class="flex gap-4">
                    @if ($this->user)
                        @if ($this->user->id === $listing->creator_id)
                            <flux:button href="{{ route('conversations.index') }}" wire:navigate>
                                View conversations
                            </flux:button>
                    @else
                        <flux:button href="{{ route('listings.message', $listing) }}" variant="primary" wire:navigate>
                            Send message
                        </flux:button>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <div class="mt-12">
        <flux:text>{{ $listing->description }}</flux:text>
    </div>
</section>
