<?php

use App\Models\Conversation;
use App\Models\Listing;
use App\Models\Marketplace;
use Illuminate\Support\Facades\Auth;
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
    public function conversations()
    {
        $userId = $this->user->id;
        $marketplaceId = $this->marketplace->id;

        $listingIds = Listing::where('marketplace_id', $marketplaceId)->pluck('id');

        return Conversation::where(function ($query) use ($userId) {
            $query->where('user_id', $userId)
                ->orWhere('listing_creator_id', $userId);
        })
            ->whereIn('listing_id', $listingIds)
            ->with(['listing', 'user', 'listingCreator', 'messages' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->latest()
            ->get()
            ->sortByDesc(function ($conversation) {
                return $conversation->messages->first()?->created_at ?? $conversation->created_at;
            })
            ->values();
    }
}
?>

<section class="mx-auto max-w-4xl">
    @if ($this->conversations->isEmpty())
        <div class="flex flex-col items-center justify-center py-12">
            <flux:heading>No conversations yet in {{ $marketplace->name }}.</flux:heading>
            <flux:text>Start by sending a message on a listing you're interested in.</flux:text>
        </div>
    @else
        <div class="flex flex-wrap justify-between gap-x-6 gap-y-4">
            <flux:heading size="xl">{{ $marketplace->name }} Conversations</flux:heading>
        </div>

        <div class="mt-8">
            <hr role="presentation" class="w-full border-t border-zinc-950/10 dark:border-white/10" />
            <div class="divide-y divide-zinc-100 overflow-hidden dark:divide-white/5 dark:text-white">
                @foreach ($this->conversations as $conversation)
                    @php
                        $otherUser = $conversation->otherUser($this->user);
                    @endphp
                    <div
                        wire:key="conversation-{{ $conversation->id }}"
                        class="relative flex items-center justify-between gap-4 py-4"
                    >
                        <div class="flex-1">
                            <flux:heading class="leading-6!">
                                <a href="{{ route('marketplaces.conversations.show', [$marketplace, $conversation]) }}" wire:navigate>
                                    {{ $conversation->listing->title }}
                                </a>
                            </flux:heading>
                            <flux:text size="sm" variant="muted">
                                @if ($otherUser)
                                    With {{ $otherUser->name ?? $otherUser->email }}
                                @endif
                            </flux:text>
                        </div>
                        <div class="flex shrink-0 items-center gap-x-4">
                            <flux:button href="{{ route('marketplaces.conversations.show', [$marketplace, $conversation]) }}" variant="ghost" wire:navigate>
                                View
                            </flux:button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</section>
