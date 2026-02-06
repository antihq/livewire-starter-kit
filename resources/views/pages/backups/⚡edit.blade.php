<?php

use App\Models\Backup;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component
{
    public Backup $backup;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|exists:backup_disks,id')]
    public ?int $backupDiskId = null;

    public array $selectedDatabases = [];

    #[Validate('nullable|string')]
    public ?string $directories = null;

    #[Validate('required|integer|min:1')]
    public int $numberOfBackupsToRetain = 7;

    #[Validate('required|string')]
    public string $frequency = 'daily';

    #[Validate('nullable|string|max:255')]
    public ?string $customCron = null;

    public ?bool $notificationOnFailure = null;

    public ?bool $notificationOnSuccess = null;

    #[Validate('nullable|email|max:255')]
    public ?string $notificationEmail = null;

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
    public function backupDisks()
    {
        return $this->team->backupDisks;
    }

    #[Computed]
    public function databases()
    {
        return $this->backup->server->databases;
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
        $this->authorize('update', $this->backup);

        $this->name = $this->backup->name;
        $this->backupDiskId = $this->backup->backup_disk_id;
        $this->selectedDatabases = $this->backup->databases->pluck('id')->toArray();
        $this->directories = $this->backup->directories;
        $this->numberOfBackupsToRetain = $this->backup->number_of_backups_to_retain;
        $this->frequency = $this->backup->frequency;
        $this->customCron = $this->backup->custom_cron;
        $this->notificationOnFailure = $this->backup->notification_on_failure;
        $this->notificationOnSuccess = $this->backup->notification_on_success;
        $this->notificationEmail = $this->backup->notification_email;
    }

    public function update(): void
    {
        $this->validate();

        if ($this->frequency === 'custom' && empty($this->customCron)) {
            $this->addError('customCron', 'The custom cron field is required when frequency is custom.');

            return;
        }

        if (($this->notificationOnFailure || $this->notificationOnSuccess) && empty($this->notificationEmail)) {
            $this->addError('notificationEmail', 'Email is required when notifications are enabled.');

            return;
        }

        $this->backup->update([
            'name' => $this->name,
            'backup_disk_id' => $this->backupDiskId,
            'directories' => $this->directories,
            'number_of_backups_to_retain' => $this->numberOfBackupsToRetain,
            'frequency' => $this->frequency,
            'custom_cron' => $this->frequency === 'custom' ? $this->customCron : null,
            'notification_on_failure' => $this->notificationOnFailure ?? false,
            'notification_on_success' => $this->notificationOnSuccess ?? false,
            'notification_email' => $this->notificationEmail,
        ]);

        $this->backup->databases()->sync($this->selectedDatabases);

        $this->redirectRoute('servers.backups.index', $this->backup->server_id, navigate: true);
    }
};
?>

<div class="mx-auto max-w-lg">
    <form wire:submit="update" class="space-y-8">
        <flux:heading size="lg">Edit Backup</flux:heading>

        <flux:input
            wire:model="name"
            label="Name"
            placeholder="e.g., Production Database Backup"
            required
        />

        <flux:select wire:model="backupDiskId" label="Backup Disk">
            <flux:select.option :value="null">None</flux:select.option>
            @foreach ($this->backupDisks as $disk)
                <flux:select.option :value="$disk->id">{{ $disk->name }}</flux:select.option>
            @endforeach
        </flux:select>

        @if ($this->databases->isNotEmpty())
            <flux:heading size="md">Databases</flux:heading>
            <div class="space-y-2">
                @foreach ($this->databases as $database)
                    <flux:checkbox
                        wire:model.live="selectedDatabases"
                        :value="$database->id"
                        label="{{ $database->name }}"
                    />
                @endforeach
            </div>
        @else
            <flux:callout>
                No databases configured on this server.
            </flux:callout>
        @endif

        <flux:textarea
            wire:model="directories"
            label="Directories and Files"
            placeholder="Enter paths, one per line&#10;/var/www/html/storage&#10;/home/user/uploads"
            rows="5"
        />
        <flux:text class="text-zinc-500 text-sm">Separate multiple paths with a new line</flux:text>

        <flux:input
            wire:model="numberOfBackupsToRetain"
            label="Number of Backups to Retain"
            type="number"
            min="1"
            required
        />

        <flux:select wire:model.live="frequency" label="Frequency" required>
            @foreach ($this->frequencyOptions as $value => $label)
                <flux:select.option :value="$value">{{ $label }}</flux:select.option>
            @endforeach
        </flux:select>

        @if ($frequency === 'custom')
            <flux:input
                wire:model="customCron"
                label="Custom Cron Expression"
                placeholder="e.g., 0 0 * * *"
                required
            />
        @endif

        <flux:heading size="md">Notifications</flux:heading>
        <flux:checkbox
            wire:model.live="notificationOnFailure"
            label="Send notifications on failure"
        />
        <flux:checkbox
            wire:model.live="notificationOnSuccess"
            label="Send notifications on success"
        />

        @if ($notificationOnFailure || $notificationOnSuccess)
            <flux:input
                wire:model="notificationEmail"
                label="Notification Email"
                type="email"
                placeholder="e.g., admin@example.com"
                required
            />
        @endif

        <div class="flex gap-3">
            <flux:spacer />
            <flux:button variant="ghost" :href="route('servers.backups.index', $backup->server_id)" wire:navigate>Cancel</flux:button>
            <flux:button variant="primary" type="submit">Save Changes</flux:button>
        </div>
    </form>
</div>
