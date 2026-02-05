<?php

use App\Models\Database;
use App\Models\Server;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component
{
    public Server $server;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Computed]
    public function user(): User
    {
        return Auth::user();
    }

    #[Computed]
    public function team()
    {
        return $this->user->currentTeam;
    }

    public function mount(): void
    {
        $this->authorize('create', Database::class);
        $this->authorize('view', $this->server);
    }

    public function create(): void
    {
        $this->validate();

        $this->team->databases()->create([
            'name' => $this->name,
            'server_id' => $this->server->id,
            'creator_id' => $this->user->id,
        ]);

        $this->redirectRoute('servers.show', $this->server->id, navigate: true);
    }
};
?>

<div class="mx-auto max-w-lg">
    <form wire:submit="create" class="space-y-8">
        <flux:heading size="lg">Add Database to {{ $server->name }}</flux:heading>

        <flux:input wire:model="name" label="Database Name" placeholder="e.g., app_production" required />

        <div class="flex gap-3">
            <flux:spacer />
            <flux:button variant="ghost" :href="route('servers.show', $server->id)" wire:navigate>Cancel</flux:button>
            <flux:button variant="primary" type="submit">Create Database</flux:button>
        </div>
    </form>
</div>
