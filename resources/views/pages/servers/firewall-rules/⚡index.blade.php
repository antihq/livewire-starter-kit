<?php

use App\Models\FirewallRule;
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
    public function firewallRules()
    {
        return $this->server->firewallRules()->latest()->get();
    }

    public function mount(): void
    {
        $this->authorize('viewAny', FirewallRule::class);
        $this->authorize('view', $this->server);
    }

    public function delete(int $id): void
    {
        $firewallRule = $this->server->firewallRules()->findOrFail($id);

        $this->authorize('delete', $firewallRule);

        $firewallRule->delete();
    }
};
?>

<div class="mx-auto max-w-lg space-y-8">
    <div class="flex items-center justify-between">
        <flux:heading size="lg">Firewall Rules for {{ $server->name }}</flux:heading>
        <div class="flex gap-2">
            <flux:button :href="route('servers.show', $server->id)" variant="ghost" wire:navigate>Back to Server</flux:button>
            <flux:button :href="route('servers.firewall-rules.create', $server->id)" variant="primary" wire:navigate>Add Firewall Rule</flux:button>
        </div>
    </div>

    @if ($this->firewallRules->isNotEmpty())
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Action</flux:table.column>
                <flux:table.column>Port</flux:table.column>
                <flux:table.column>From IP</flux:table.column>
                <flux:table.column>Created By</flux:table.column>
                <flux:table.column>Created At</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->firewallRules as $firewallRule)
                    <flux:table.row :key="$firewallRule->id">
                        <flux:table.cell>{{ $firewallRule->name }}</flux:table.cell>
                        <flux:table.cell>{{ $firewallRule->action }}</flux:table.cell>
                        <flux:table.cell>{{ $firewallRule->port }}</flux:table.cell>
                        <flux:table.cell>{{ $firewallRule->from_ip ?? 'Any' }}</flux:table.cell>
                        <flux:table.cell>{{ $firewallRule->creator?->name ?? 'Unknown' }}</flux:table.cell>
                        <flux:table.cell>{{ $firewallRule->created_at->diffForHumans() }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-2">
                                <flux:button
                                    variant="ghost"
                                    :href="route('firewall-rules.edit', $firewallRule->id)"
                                    wire:navigate
                                >
                                    Edit
                                </flux:button>
                                <flux:button
                                    variant="ghost"
                                    wire:confirm="Are you sure you want to delete this firewall rule?"
                                    wire:click="delete({{ $firewallRule->id }})"
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
        <flux:text class="text-zinc-500">No firewall rules configured on this server.</flux:text>
    @endif
</div>
