<?php

use App\Models\FirewallRule;
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

    #[Validate('required|string|in:allow,deny,reject')]
    public string $action = 'allow';

    #[Validate('required|integer|min:1|max:65535')]
    public int $port = 80;

    #[Validate('nullable|string|max:255')]
    public ?string $from_ip = null;

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
    public function actionOptions(): array
    {
        return [
            'allow' => 'Allow',
            'deny' => 'Deny',
            'reject' => 'Reject',
        ];
    }

    public function mount(): void
    {
        $this->authorize('create', FirewallRule::class);
        $this->authorize('view', $this->server);
    }

    public function create(): void
    {
        $this->validate();

        $this->team->firewallRules()->create([
            'name' => $this->name,
            'action' => $this->action,
            'port' => $this->port,
            'from_ip' => $this->from_ip,
            'server_id' => $this->server->id,
            'creator_id' => $this->user->id,
        ]);

        $this->redirectRoute('servers.firewall-rules.index', $this->server->id, navigate: true);
    }
};
?>

<div class="mx-auto max-w-lg">
    <form wire:submit="create" class="space-y-8">
        <flux:heading size="lg">Add Firewall Rule to {{ $server->name }}</flux:heading>

        <flux:input
            wire:model="name"
            label="Name"
            placeholder="e.g., HTTP Access"
            required
        />

        <flux:select wire:model="action" label="Action" required>
            @foreach ($this->actionOptions as $value => $label)
                <flux:select.option :value="$value">{{ $label }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:input
            wire:model="port"
            label="Port"
            type="number"
            min="1"
            max="65535"
            placeholder="e.g., 80, 443, 22"
            required
        />

        <flux:input
            wire:model="from_ip"
            label="From IP (Optional)"
            placeholder="e.g., 192.168.1.100"
        />

        <div class="flex gap-3">
            <flux:spacer />
            <flux:button variant="ghost" :href="route('servers.firewall-rules.index', $server->id)" wire:navigate>Cancel</flux:button>
            <flux:button variant="primary" type="submit">Create Firewall Rule</flux:button>
        </div>
    </form>
</div>
