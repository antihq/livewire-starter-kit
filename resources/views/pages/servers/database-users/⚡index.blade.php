<?php

use App\Models\DatabaseUser;
use App\Models\Server;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public Server $server;

    #[Computed]
    public function user(): User
    {
        return Auth::user();
    }

    #[Computed]
    public function databaseUsers()
    {
        return $this->server->databaseUsers()->latest()->get();
    }

    public function mount(): void
    {
        $this->authorize('viewAny', DatabaseUser::class);
        $this->authorize('view', $this->server);
    }

    public function delete(int $id): void
    {
        $databaseUser = $this->server->databaseUsers()->findOrFail($id);

        $this->authorize('delete', $databaseUser);

        $databaseUser->delete();
    }
};
?>

<div class="mx-auto max-w-lg space-y-8">
    <div class="flex items-center justify-between">
        <flux:heading size="lg">Database Users for {{ $server->name }}</flux:heading>
        <div class="flex gap-2">
            <flux:button :href="route('servers.show', $server->id)" variant="ghost" wire:navigate>Back to Server</flux:button>
            <flux:button :href="route('servers.database-users.create', $server->id)" variant="primary" wire:navigate>Create Database User</flux:button>
        </div>
    </div>

    @if ($this->databaseUsers->isNotEmpty())
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Username</flux:table.column>
                <flux:table.column>Databases</flux:table.column>
                <flux:table.column>Created By</flux:table.column>
                <flux:table.column>Created At</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->databaseUsers as $databaseUser)
                    <flux:table.row :key="$databaseUser->id">
                        <flux:table.cell>{{ $databaseUser->username }}</flux:table.cell>
                        <flux:table.cell>{{ $databaseUser->databases->pluck('name')->join(', ') ?: 'None' }}</flux:table.cell>
                        <flux:table.cell>{{ $databaseUser->creator?->name ?? 'Unknown' }}</flux:table.cell>
                        <flux:table.cell>{{ $databaseUser->created_at->diffForHumans() }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button
                                variant="ghost"
                                wire:confirm="Are you sure you want to delete this database user?"
                                wire:click="delete({{ $databaseUser->id }})"
                            >
                                Delete
                            </flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    @else
        <flux:text class="text-zinc-500">No database users configured on this server.</flux:text>
    @endif
</div>
