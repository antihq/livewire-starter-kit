<?php

use App\Models\Daemon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component
{
    public Daemon $daemon;

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
        $this->authorize('update', $this->daemon);

        $this->command = $this->daemon->command;
        $this->directory = $this->daemon->directory;
        $this->systemUser = $this->daemon->user;
        $this->processes = $this->daemon->processes;
        $this->stop_wait_seconds = $this->daemon->stop_wait_seconds;
        $this->stop_signal = $this->daemon->stop_signal;
    }

    public function update(): void
    {
        $this->validate();

        $this->daemon->update([
            'command' => $this->command,
            'directory' => $this->directory,
            'user' => $this->systemUser,
            'processes' => $this->processes,
            'stop_wait_seconds' => $this->stop_wait_seconds,
            'stop_signal' => $this->stop_signal,
        ]);

        $this->redirectRoute('servers.daemons.index', $this->daemon->server_id, navigate: true);
    }
};
?>

<div class="mx-auto max-w-lg">
    <form wire:submit="update" class="space-y-8">
        <flux:heading size="lg">Edit Daemon</flux:heading>

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
            <flux:button variant="ghost" :href="route('servers.daemons.index', $daemon->server_id)" wire:navigate>Cancel</flux:button>
            <flux:button variant="primary" type="submit">Save Changes</flux:button>
        </div>
    </form>
</div>
