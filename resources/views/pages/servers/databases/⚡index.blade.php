<?php

use App\Models\Database;
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
    public function databases()
    {
        return $this->server->databases()->latest()->get();
    }

    public function mount(): void
    {
        $this->authorize('viewAny', Database::class);
        $this->authorize('view', $this->server);
    }

    public function delete(int $id): void
    {
        $database = $this->server->databases()->findOrFail($id);

        $this->authorize('delete', $database);

        $database->delete();
    }
};
?>

<div class="mx-auto max-w-lg space-y-8">
    <div class="flex items-center justify-between">
        <flux:heading size="lg">Databases for {{ $server->name }}</flux:heading>
        <div class="flex gap-2">
            <flux:button :href="route('servers.show', $server->id)" variant="ghost" wire:navigate>Back to Server</flux:button>
            <flux:button :href="route('servers.databases.create', $server->id)" variant="primary" wire:navigate>Create Database</flux:button>
        </div>
    </div>

    @if ($this->databases->isNotEmpty())
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Database Name</flux:table.column>
                <flux:table.column>Created By</flux:table.column>
                <flux:table.column>Created At</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->databases as $database)
                    <flux:table.row :key="$database->id">
                        <flux:table.cell>{{ $database->name }}</flux:table.cell>
                        <flux:table.cell>{{ $database->creator?->name ?? 'Unknown' }}</flux:table.cell>
                        <flux:table.cell>{{ $database->created_at->diffForHumans() }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button
                                variant="ghost"
                                wire:confirm="Are you sure you want to delete this database?"
                                wire:click="delete({{ $database->id }})"
                            >
                                Delete
                            </flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    @else
        <flux:text class="text-zinc-500">No databases configured on this server.</flux:text>
    @endif
</div>
