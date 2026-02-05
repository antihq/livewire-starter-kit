<?php

use App\Models\Server;
use App\Models\Site;
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
    public function sites()
    {
        return $this->server->sites()->latest()->get();
    }

    public function mount(): void
    {
        $this->authorize('viewAny', Site::class);
        $this->authorize('view', $this->server);
    }

    public function delete(int $id): void
    {
        $site = $this->server->sites()->findOrFail($id);

        $this->authorize('delete', $site);

        $site->delete();
    }
};
?>

<div class="mx-auto max-w-lg space-y-8">
    <div class="flex items-center justify-between">
        <flux:heading size="lg">Sites for {{ $server->name }}</flux:heading>
        <div class="flex gap-2">
            <flux:button :href="route('servers.show', $server->id)" variant="ghost" wire:navigate>Back to Server</flux:button>
            <flux:button :href="route('servers.sites.create', $server->id)" variant="primary" wire:navigate>Create Site</flux:button>
        </div>
    </div>

    @if ($this->sites->isNotEmpty())
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Hostname</flux:table.column>
                <flux:table.column>PHP Version</flux:table.column>
                <flux:table.column>Site Type</flux:table.column>
                <flux:table.column>Created At</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->sites as $site)
                    <flux:table.row :key="$site->id">
                        <flux:table.cell>{{ $site->hostname }}</flux:table.cell>
                        <flux:table.cell>{{ $site->php_version }}</flux:table.cell>
                        <flux:table.cell>{{ ucfirst($site->site_type) }}</flux:table.cell>
                        <flux:table.cell>{{ $site->created_at->diffForHumans() }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button
                                variant="ghost"
                                :href="route('sites.show', $site->id)"
                                wire:navigate
                            >
                                View
                            </flux:button>
                            <flux:button
                                variant="ghost"
                                wire:confirm="Are you sure you want to delete this site?"
                                wire:click="delete({{ $site->id }})"
                            >
                                Delete
                            </flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    @else
        <flux:text class="text-zinc-500">No sites configured on this server.</flux:text>
    @endif
</div>
