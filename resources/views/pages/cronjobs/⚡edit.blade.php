<?php

use App\Models\Cronjob;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component
{
    public Cronjob $cronjob;

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
        $this->authorize('update', $this->cronjob);

        $this->command = $this->cronjob->command;
        $this->systemUser = $this->cronjob->user;
        $this->frequency = $this->cronjob->frequency;
        $this->custom_cron = $this->cronjob->custom_cron;
    }

    public function update(): void
    {
        $this->validate();

        if ($this->frequency === 'custom' && empty($this->custom_cron)) {
            $this->addError('custom_cron', 'The custom cron field is required when frequency is custom.');

            return;
        }

        $this->cronjob->update([
            'command' => $this->command,
            'user' => $this->systemUser,
            'frequency' => $this->frequency,
            'custom_cron' => $this->frequency === 'custom' ? $this->custom_cron : null,
        ]);

        $this->redirectRoute('servers.cronjobs.index', $this->cronjob->server_id, navigate: true);
    }
};
?>

<div class="mx-auto max-w-lg">
    <form wire:submit="update" class="space-y-8">
        <flux:heading size="lg">Edit Cronjob</flux:heading>

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
            <flux:button variant="ghost" :href="route('servers.cronjobs.index', $cronjob->server_id)" wire:navigate>Cancel</flux:button>
            <flux:button variant="primary" type="submit">Save Changes</flux:button>
        </div>
    </form>
</div>
