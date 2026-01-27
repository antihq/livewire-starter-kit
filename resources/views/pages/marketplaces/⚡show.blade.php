<?php

use App\Models\Marketplace;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public Marketplace $marketplace;

    public $name = '';

    public function mount()
    {
        $this->authorize('view', $this->marketplace);

        $this->name = $this->marketplace->name;
    }

    public function update()
    {
        $this->authorize('update', $this->marketplace);

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $this->marketplace
            ->fill([
                'name' => $this->name,
            ])
            ->save();

        Flux::toast('Marketplace name has been saved.', variant: 'success');
    }

    public function delete()
    {
        $this->authorize('delete', $this->marketplace);

        $this->marketplace->delete();

        return $this->redirectRoute('marketplaces.index');
    }

    #[Computed]
    public function listings()
    {
        return $this->marketplace->listings;
    }
};
?>

<section class="mx-auto max-w-6xl space-y-8">
    <flux:heading size="xl">Marketplace settings</flux:heading>

    <div class="space-y-14">
        <div class="space-y-6">
            <header class="space-y-1">
                <flux:heading>Marketplace name</flux:heading>
                <flux:text>The name of your marketplace.</flux:text>
            </header>

            <form wire:submit="update" class="w-full max-w-lg space-y-8">
                <flux:input
                    wire:model="name"
                    label="Marketplace name"
                    type="text"
                    :readonly="! Gate::check('update', $marketplace)"
                    :variant="! Gate::check('update', $marketplace) ? 'filled' : null"
                    required
                    autofocus
                />

                @if (Gate::check('update', $marketplace))
                    <div class="flex items-center gap-4">
                        <div class="flex items-center justify-end">
                            <flux:button variant="primary" type="submit" class="w-full">Save changes</flux:button>
                        </div>
                    </div>
                @endif
            </form>
        </div>

        <div class="space-y-6">
            <header class="space-y-1">
                <flux:heading size="lg">Listings</flux:heading>
                <flux:text>Manage listings for this marketplace.</flux:text>
            </header>

            <div class="flex items-center gap-4">
                <flux:button href="{{ route('marketplaces.listings.create', $marketplace) }}" wire:navigate>
                    Create listing
                </flux:button>
            </div>

            @forelse ($this->listings as $listing)
                <flux:button href="{{ route('marketplaces.listings.show', [$marketplace, $listing]) }}" wire:navigate variant="ghost" class="w-full justify-start !px-0">
                    <div class="w-full border-b border-white/10 pb-4">
                        <div class="space-y-2">
                            <flux:heading size="md">{{ $listing->title }}</flux:heading>
                            <flux:text>{{ $listing->description }}</flux:text>
                        </div>
                    </div>
                </flux:button>
            @empty
                <flux:text>No listings yet</flux:text>
            @endforelse
        </div>

        @if (Gate::check('delete', $marketplace))
            <div class="space-y-6">
                <header class="space-y-1">
                    <flux:heading>Delete marketplace</flux:heading>
                </header>

                <flux:modal.trigger name="delete">
                    <flux:button variant="danger">Delete marketplace</flux:button>
                </flux:modal.trigger>

                <flux:modal name="delete" class="w-full max-w-xs sm:max-w-md">
                    <div class="space-y-6 sm:space-y-4">
                        <div>
                            <flux:heading>Delete marketplace?</flux:heading>
                            <flux:text class="mt-2">
                                You're about to delete this marketplace. This action cannot be reversed.
                            </flux:text>
                        </div>
                        <div class="flex flex-col-reverse items-center justify-end gap-3 *:w-full sm:flex-row sm:*:w-auto">
                            <flux:modal.close>
                                <flux:button variant="ghost" class="w-full sm:w-auto">Cancel</flux:button>
                            </flux:modal.close>
                            <flux:button wire:click="delete" variant="primary">Delete</flux:button>
                        </div>
                    </div>
                </flux:modal>
            </div>
        @endif
    </div>
</section>
