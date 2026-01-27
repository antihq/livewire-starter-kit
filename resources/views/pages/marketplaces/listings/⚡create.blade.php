<?php

use App\Models\Listing;
use App\Models\Marketplace;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
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

        $this->marketplace->listings()->create([
            'title' => $this->title,
            'description' => $this->description,
            'user_id' => Auth::id(),
        ]);

        return $this->redirectRoute('marketplaces.edit', $this->marketplace);
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

<section class="mx-auto max-w-6xl space-y-8">
    <flux:heading size="xl">Create listing</flux:heading>

    <div class="space-y-14">
        <div class="space-y-6">
            <header class="space-y-1">
                <flux:heading size="lg">Listing details</flux:heading>
                <flux:text>Create a new listing for {{ $marketplace->name }}.</flux:text>
            </header>

            <form wire:submit="create" class="w-full max-w-lg space-y-8">
                <flux:input wire:model="title" label="Listing title" type="text" required autofocus />
                <flux:textarea wire:model="description" label="Description" required />

                <div class="flex items-center gap-4">
                    <div class="flex items-center justify-end">
                        <flux:button variant="primary" type="submit" class="w-full">Create listing</flux:button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
