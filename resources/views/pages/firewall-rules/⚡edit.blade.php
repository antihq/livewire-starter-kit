<?php

use App\Models\FirewallRule;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component
{
    public FirewallRule $firewallRule;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Computed]
    public function user(): User
    {
        return Auth::user();
    }

    public function mount(): void
    {
        $this->authorize('update', $this->firewallRule);

        $this->name = $this->firewallRule->name;
    }

    public function update(): void
    {
        $this->validate();

        $this->firewallRule->update([
            'name' => $this->name,
        ]);

        $this->redirectRoute('servers.firewall-rules.index', $this->firewallRule->server_id, navigate: true);
    }
};
?>

<div class="mx-auto max-w-lg">
    <form wire:submit="update" class="space-y-8">
        <flux:heading size="lg">Edit Firewall Rule</flux:heading>

        <flux:input
            wire:model="name"
            label="Name"
            placeholder="e.g., HTTP Access"
            required
        />

        <div class="space-y-4">
            <flux:text class="font-medium">Rule Details (Read-only)</flux:text>
            <div class="space-y-2">
                <flux:text class="text-sm">Action: <strong>{{ $firewallRule->action }}</strong></flux:text>
                <flux:text class="text-sm">Port: <strong>{{ $firewallRule->port }}</strong></flux:text>
                <flux:text class="text-sm">From IP: <strong>{{ $firewallRule->from_ip ?? 'Any' }}</strong></flux:text>
            </div>
        </div>

        <div class="flex gap-3">
            <flux:spacer />
            <flux:button variant="ghost" :href="route('servers.firewall-rules.index', $firewallRule->server_id)" wire:navigate>Cancel</flux:button>
            <flux:button variant="primary" type="submit">Save Changes</flux:button>
        </div>
    </form>
</div>
