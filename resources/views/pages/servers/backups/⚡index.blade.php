<?php

use App\Models\Backup;
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
    public function backups()
    {
        return $this->server->backups()->with(['creator', 'backupDisk', 'databases'])->latest()->get();
    }

    #[Computed]
    public function frequencyLabels(): array
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
        $this->authorize('viewAny', Backup::class);
        $this->authorize('view', $this->server);
    }

    public function delete(int $id): void
    {
        $backup = $this->server->backups()->findOrFail($id);

        $this->authorize('delete', $backup);

        $backup->delete();
    }
};
?>

<div class="mx-auto max-w-lg space-y-8">
    <div class="flex items-center justify-between">
        <flux:heading size="lg">Backups for {{ $server->name }}</flux:heading>
        <div class="flex gap-2">
            <flux:button :href="route('servers.show', $server->id)" variant="ghost" wire:navigate>Back to Server</flux:button>
            <flux:button :href="route('servers.backups.create', $server->id)" variant="primary" wire:navigate>Create Backup</flux:button>
        </div>
    </div>

    @if ($this->backups->isNotEmpty())
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Backup Disk</flux:table.column>
                <flux:table.column>Databases</flux:table.column>
                <flux:table.column>Directories</flux:table.column>
                <flux:table.column>Retention</flux:table.column>
                <flux:table.column>Frequency</flux:table.column>
                <flux:table.column>Notifications</flux:table.column>
                <flux:table.column>Created At</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->backups as $backup)
                    <flux:table.row :key="$backup->id">
                        <flux:table.cell>{{ $backup->name }}</flux:table.cell>
                        <flux:table.cell>{{ $backup->backupDisk?->name ?? 'None' }}</flux:table.cell>
                        <flux:table.cell>
                            @if ($backup->databases->isNotEmpty())
                                {{ $backup->databases->pluck('name')->join(', ') }}
                            @else
                                <flux:text class="text-zinc-500">None</flux:text>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($backup->directories)
                                <flux:text class="text-xs truncate max-w-48">{{ str_replace("\n", ', ', $backup->directories) }}</flux:text>
                            @else
                                <flux:text class="text-zinc-500">None</flux:text>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>{{ $backup->number_of_backups_to_retain }}</flux:table.cell>
                        <flux:table.cell>{{ $this->frequencyLabels[$backup->frequency] ?? $backup->frequency }}</flux:table.cell>
                        <flux:table.cell>
                            @if ($backup->notification_on_failure || $backup->notification_on_success)
                                @if ($backup->notification_on_failure && $backup->notification_on_success)
                                    <flux:text class="text-xs">Failure &amp; Success</flux:text>
                                @elseif ($backup->notification_on_failure)
                                    <flux:text class="text-xs">Failure only</flux:text>
                                @else
                                    <flux:text class="text-xs">Success only</flux:text>
                                @endif
                            @else
                                <flux:text class="text-zinc-500 text-xs">None</flux:text>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>{{ $backup->created_at->diffForHumans() }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-2">
                                <flux:button
                                    variant="ghost"
                                    :href="route('backups.edit', $backup->id)"
                                    wire:navigate
                                >
                                    Edit
                                </flux:button>
                                <flux:button
                                    variant="ghost"
                                    wire:confirm="Are you sure you want to delete this backup?"
                                    wire:click="delete({{ $backup->id }})"
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
        <flux:text class="text-zinc-500">No backups configured on this server.</flux:text>
    @endif
</div>
