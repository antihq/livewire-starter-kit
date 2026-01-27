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

<section class="mx-auto max-w-6xl space-y-8">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Marketplaces</flux:heading>

        <flux:button href="{{ route('marketplaces.create') }}" wire:navigate>
            Create marketplace
        </flux:button>
    </div>

    <div class="space-y-6">
        @forelse ($this->marketplaces as $marketplace)
            <div class="flex items-center justify-between rounded-lg border bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
                <div class="space-y-1">
                    <flux:heading size="md">{{ $marketplace->name }}</flux:heading>
                    <flux:text size="sm">Created {{ $marketplace->created_at->diffForHumans() }}</flux:text>
                </div>

                <div class="flex items-center gap-4">
                    <flux:button href="{{ route('marketplaces.edit', $marketplace) }}" wire:navigate>
                        Settings
                    </flux:button>
                </div>
            </div>
        @empty
            <div class="rounded-lg border border-dashed p-12 text-center dark:border-gray-700">
                <flux:heading size="md">No marketplaces yet</flux:heading>
                <flux:text class="mt-2">Create your first marketplace to get started.</flux:text>
            </div>
        @endforelse
    </div>
</section>
