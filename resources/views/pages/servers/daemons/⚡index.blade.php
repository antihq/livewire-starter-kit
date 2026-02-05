<?php

use App\Models\Daemon;
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
    public function daemons()
    {
        return $this->server->daemons()->latest()->get();
    }

    public function mount(): void
    {
        $this->authorize('viewAny', Daemon::class);
        $this->authorize('view', $this->server);
    }

    public function delete(int $id): void
    {
        $daemon = $this->server->daemons()->findOrFail($id);

        $this->authorize('delete', $daemon);

        $daemon->delete();
    }
};
?>

<div class="mx-auto max-w-lg space-y-8">
    <div class="flex items-center justify-between">
        <flux:heading size="lg">Daemons for {{ $server->name }}</flux:heading>
        <div class="flex gap-2">
            <flux:button :href="route('servers.show', $server->id)" variant="ghost" wire:navigate>Back to Server</flux:button>
            <flux:button :href="route('servers.daemons.create', $server->id)" variant="primary" wire:navigate>Create Daemon</flux:button>
        </div>
    </div>

    @if ($this->daemons->isNotEmpty())
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Command</flux:table.column>
                <flux:table.column>Directory</flux:table.column>
                <flux:table.column>User</flux:table.column>
                <flux:table.column>Processes</flux:table.column>
                <flux:table.column>Stop Signal</flux:table.column>
                <flux:table.column>Created By</flux:table.column>
                <flux:table.column>Created At</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->daemons as $daemon)
                    <flux:table.row :key="$daemon->id">
                        <flux:table.cell>{{ $daemon->command }}</flux:table.cell>
                        <flux:table.cell>{{ $daemon->directory ?? '-' }}</flux:table.cell>
                        <flux:table.cell>{{ $daemon->user }}</flux:table.cell>
                        <flux:table.cell>{{ $daemon->processes }}</flux:table.cell>
                        <flux:table.cell>{{ $daemon->stop_signal }}</flux:table.cell>
                        <flux:table.cell>{{ $daemon->creator?->name ?? 'Unknown' }}</flux:table.cell>
                        <flux:table.cell>{{ $daemon->created_at->diffForHumans() }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-2">
                                <flux:button
                                    variant="ghost"
                                    :href="route('daemons.edit', $daemon->id)"
                                    wire:navigate
                                >
                                    Edit
                                </flux:button>
                                <flux:button
                                    variant="ghost"
                                    wire:confirm="Are you sure you want to delete this daemon?"
                                    wire:click="delete({{ $daemon->id }})"
                                >
                                    Delete
                                </flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    @else
        <flux:text class="text-zinc-500">No daemons configured on this server.</flux:text>
    @endif
</div>
