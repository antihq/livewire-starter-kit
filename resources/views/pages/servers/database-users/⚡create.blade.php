<?php

use App\Models\DatabaseUser;
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
    public string $username = '';

    #[Validate('required|string|min:8')]
    public string $password = '';

    #[Validate('required|array')]
    public array $selectedDatabases = [];

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

    #[Computed]
    public function availableDatabases()
    {
        return $this->server->databases()->pluck('name', 'id');
    }

    public function mount(): void
    {
        $this->authorize('create', DatabaseUser::class);
        $this->authorize('view', $this->server);
    }

    public function create(): void
    {
        $this->validate();

        $databaseUser = $this->team->databaseUsers()->create([
            'username' => $this->username,
            'password' => $this->password,
            'server_id' => $this->server->id,
            'creator_id' => $this->user->id,
        ]);

        $databaseUser->databases()->attach($this->selectedDatabases);

        $this->redirectRoute('servers.database-users.index', $this->server->id, navigate: true);
    }
};
?>

<div class="mx-auto max-w-lg">
    <form wire:submit="create" class="space-y-8">
        <flux:heading size="lg">Add Database User to {{ $server->name }}</flux:heading>

        <flux:input wire:model="username" label="Username" placeholder="e.g., db_user_1" required />

        <flux:input wire:model="password" label="Password" type="password" placeholder="Enter a secure password" required />

        <flux:checkbox.group wire:model="selectedDatabases" label="Databases">
            @forelse ($this->availableDatabases as $id => $name)
                <flux:checkbox :value="$id" :label="$name" />
            @empty
                <flux:text class="text-zinc-500">No databases available. Create a database first.</flux:text>
            @endforelse
        </flux:checkbox.group>

        <div class="flex gap-3">
            <flux:spacer />
            <flux:button variant="ghost" :href="route('servers.database-users.index', $server->id)" wire:navigate>Cancel</flux:button>
            <flux:button variant="primary" type="submit">Create Database User</flux:button>
        </div>
    </form>
</div>
