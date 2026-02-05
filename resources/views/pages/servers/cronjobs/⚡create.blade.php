<?php

use App\Models\Cronjob;
use App\Models\Server;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component
{
    public Server $server;

    #[Validate('required|string|max:1000')]
    public string $command = '';

    #[Validate('required|string|max:255')]
    public string $systemUser = 'fuse';

    #[Validate('required|string')]
    public string $frequency = 'daily';

    #[Validate('nullable|string|max:255')]
    public ?string $custom_cron = null;

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
    public function frequencyOptions(): array
    {
        return [
            'every_minute' => 'Every minute',
            'every_5_minutes' => 'Every 5 minutes',
            'hourly' => 'Hourly',
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'on_reboot' => 'On Reboot',
            'custom' => 'Custom',
        ];
    }

    public function mount(): void
    {
        $this->authorize('create', Cronjob::class);
        $this->authorize('view', $this->server);
    }

    public function create(): void
    {
        $this->validate();

        if ($this->frequency === 'custom' && empty($this->custom_cron)) {
            $this->addError('custom_cron', 'The custom cron field is required when frequency is custom.');

            return;
        }

        $this->team->cronjobs()->create([
            'command' => $this->command,
            'user' => $this->systemUser,
            'frequency' => $this->frequency,
            'custom_cron' => $this->frequency === 'custom' ? $this->custom_cron : null,
            'server_id' => $this->server->id,
            'creator_id' => $this->user->id,
        ]);

        $this->redirectRoute('servers.cronjobs.index', $this->server->id, navigate: true);
    }
};
?>

<div class="mx-auto max-w-lg">
    <form wire:submit="create" class="space-y-8">
        <flux:heading size="lg">Add Cronjob to {{ $server->name }}</flux:heading>

        <flux:input
            wire:model="command"
            label="Command"
            placeholder="e.g., php /var/www/html/artisan schedule:run"
            required
        />

        <flux:input
            wire:model="systemUser"
            label="System User"
            placeholder="e.g., fuse, root"
            required
        />

        <flux:select wire:model.live="frequency" label="Frequency" required>
            @foreach ($this->frequencyOptions as $value => $label)
                <flux:select.option :value="$value">{{ $label }}</flux:select.option>
            @endforeach
        </flux:select>

        @if ($frequency === 'custom')
            <flux:input
                wire:model="custom_cron"
                label="Custom Cron Expression"
                placeholder="e.g., 0 0 * * *"
                required
            />
        @endif

        <div class="flex gap-3">
            <flux:spacer />
            <flux:button variant="ghost" :href="route('servers.cronjobs.index', $server->id)" wire:navigate>Cancel</flux:button>
            <flux:button variant="primary" type="submit">Create Cronjob</flux:button>
        </div>
    </form>
</div>
