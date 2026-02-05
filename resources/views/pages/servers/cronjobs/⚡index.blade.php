<?php

use App\Models\Cronjob;
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
    public function cronjobs()
    {
        return $this->server->cronjobs()->latest()->get();
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
        $this->authorize('viewAny', Cronjob::class);
        $this->authorize('view', $this->server);
    }

    public function delete(int $id): void
    {
        $cronjob = $this->server->cronjobs()->findOrFail($id);

        $this->authorize('delete', $cronjob);

        $cronjob->delete();
    }
};
?>

<div class="mx-auto max-w-lg space-y-8">
    <div class="flex items-center justify-between">
        <flux:heading size="lg">Cronjobs for {{ $server->name }}</flux:heading>
        <div class="flex gap-2">
            <flux:button :href="route('servers.show', $server->id)" variant="ghost" wire:navigate>Back to Server</flux:button>
            <flux:button :href="route('servers.cronjobs.create', $server->id)" variant="primary" wire:navigate>Create Cronjob</flux:button>
        </div>
    </div>

    @if ($this->cronjobs->isNotEmpty())
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Command</flux:table.column>
                <flux:table.column>User</flux:table.column>
                <flux:table.column>Frequency</flux:table.column>
                <flux:table.column>Created By</flux:table.column>
                <flux:table.column>Created At</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->cronjobs as $cronjob)
                    <flux:table.row :key="$cronjob->id">
                        <flux:table.cell>{{ $cronjob->command }}</flux:table.cell>
                        <flux:table.cell>{{ $cronjob->user }}</flux:table.cell>
                        <flux:table.cell>{{ $this->frequencyLabels[$cronjob->frequency] ?? $cronjob->frequency }}</flux:table.cell>
                        <flux:table.cell>{{ $cronjob->creator?->name ?? 'Unknown' }}</flux:table.cell>
                        <flux:table.cell>{{ $cronjob->created_at->diffForHumans() }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-2">
                                <flux:button
                                    variant="ghost"
                                    :href="route('cronjobs.edit', $cronjob->id)"
                                    wire:navigate
                                >
                                    Edit
                                </flux:button>
                                <flux:button
                                    variant="ghost"
                                    wire:confirm="Are you sure you want to delete this cronjob?"
                                    wire:click="delete({{ $cronjob->id }})"
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
        <flux:text class="text-zinc-500">No cronjobs configured on this server.</flux:text>
    @endif
</div>
