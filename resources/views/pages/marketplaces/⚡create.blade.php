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

        $team->marketplaces()->create([
            'name' => $this->name,
        ]);

        return $this->redirectRoute('marketplaces.index');
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
    <flux:heading size="xl">Create marketplace</flux:heading>

    <div class="space-y-14">
        <div class="space-y-6">
            <header class="space-y-1">
                <flux:heading size="lg">Marketplace details</flux:heading>
                <flux:text>Create a new marketplace for your team.</flux:text>
            </header>

            <form wire:submit="create" class="w-full max-w-lg space-y-8">
                <flux:input wire:model="name" label="Marketplace name" type="text" required autofocus />

                <div class="flex items-center gap-4">
                    <div class="flex items-center justify-end">
                        <flux:button variant="primary" type="submit" class="w-full">Create marketplace</flux:button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
