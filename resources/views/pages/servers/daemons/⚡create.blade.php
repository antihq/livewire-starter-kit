<?php

use App\Models\Daemon;
use App\Models\Server;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component
{
    public Server $server;

    #[Validate('required|string|max:2000')]
    public string $command = '';

    #[Validate('nullable|string|max:500')]
    public ?string $directory = null;

    #[Validate('required|string|max:255')]
    public string $systemUser = 'fuse';

    #[Validate('required|integer|min:1')]
    public int $processes = 1;

    #[Validate('required|integer|min:0')]
    public int $stop_wait_seconds = 10;

    #[Validate('required|string')]
    public string $stop_signal = 'TERM';

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
    public function stopSignalOptions(): array
    {
        return [
            'TERM' => 'TERM (SIGTERM)',
            'KILL' => 'KILL (SIGKILL)',
            'HUP' => 'HUP (SIGHUP)',
            'INT' => 'INT (SIGINT)',
        ];
    }

    public function mount(): void
    {
        $this->authorize('create', Daemon::class);
        $this->authorize('view', $this->server);
    }

    public function create(): void
    {
        $this->validate();

        $this->team->daemons()->create([
            'command' => $this->command,
            'directory' => $this->directory,
            'user' => $this->systemUser,
            'processes' => $this->processes,
            'stop_wait_seconds' => $this->stop_wait_seconds,
            'stop_signal' => $this->stop_signal,
            'server_id' => $this->server->id,
            'creator_id' => $this->user->id,
        ]);

        $this->redirectRoute('servers.daemons.index', $this->server->id, navigate: true);
    }
};
?>

<div class="mx-auto max-w-lg">
    <form wire:submit="create" class="space-y-8">
        <flux:heading size="lg">Add Daemon to {{ $server->name }}</flux:heading>

        <flux:input
            wire:model="command"
            label="Command"
            placeholder="e.g., php /var/www/html/artisan horizon"
            required
        />

        <flux:input
            wire:model="directory"
            label="Directory"
            placeholder="e.g., /var/www/html"
        />

        <flux:input
            wire:model="systemUser"
            label="System User"
            placeholder="e.g., fuse, root"
            required
        />

        <flux:input
            wire:model="processes"
            label="Number of Processes"
            type="number"
            min="1"
            required
        />

        <flux:input
            wire:model="stop_wait_seconds"
            label="Stop Wait Seconds"
            type="number"
            min="0"
            required
        />

        <flux:select wire:model="stop_signal" label="Stop Signal" required>
            @foreach ($this->stopSignalOptions as $value => $label)
                <flux:select.option :value="$value">{{ $label }}</flux:select.option>
            @endforeach
        </flux:select>

        <div class="flex gap-3">
            <flux:spacer />
            <flux:button variant="ghost" :href="route('servers.daemons.index', $server->id)" wire:navigate>Cancel</flux:button>
            <flux:button variant="primary" type="submit">Create Daemon</flux:button>
        </div>
    </form>
</div>
