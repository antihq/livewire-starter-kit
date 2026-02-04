<?php

use App\Models\Server;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public ?int $deletingId = null;

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
    public function servers()
    {
        return $this->team->servers()->latest()->get();
    }

    public function mount(): void
    {
        $this->authorize('viewAny', Server::class);
    }

    public function delete(int $id): void
    {
        $server = $this->team->servers()->findOrFail($id);

        $this->authorize('delete', $server);

        $server->delete();
    }
};
?>

<div class="mx-auto max-w-lg space-y-8">
    <div class="flex items-center justify-between">
        <flux:heading size="lg">Servers</flux:heading>
        <flux:button :href="route('servers.create')" variant="primary" wire:navigate>Create Server</flux:button>
    </div>

    @if ($this->servers->isNotEmpty())
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Public IP</flux:table.column>
                <flux:table.column>Created By</flux:table.column>
                <flux:table.column>Created At</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->servers as $server)
                    <flux:table.row :key="$server->id">
                        <flux:table.cell>{{ $server->name }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:text class="max-w-md truncate font-mono text-xs">
                                {{ $server->public_ip }}
                            </flux:text>
                        </flux:table.cell>
                        <flux:table.cell>{{ $server->creator?->name ?? 'Unknown' }}</flux:table.cell>
                        <flux:table.cell>{{ $server->created_at->diffForHumans() }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button
                                variant="ghost"
                                :href="route('servers.show', $server->id)"
                                wire:navigate
                            >
                                View
                            </flux:button>
                            <flux:button
                                variant="ghost"
                                wire:confirm="Are you sure you want to delete this server?"
                                wire:click="delete({{ $server->id }})"
                            >
                                Delete
                            </flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    @endif
</div>
