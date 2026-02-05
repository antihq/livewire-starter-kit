<?php

use App\Models\BackupDisk;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public ?int $deletingId = null;

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
        return $this->team->backupDisks()->with('creator')->latest()->get();
    }

    public function mount(): void
    {
        $this->authorize('viewAny', BackupDisk::class);
    }

    public function delete(int $id): void
    {
        $backupDisk = $this->team->backupDisks()->findOrFail($id);

        $this->authorize('delete', $backupDisk);

        $backupDisk->delete();
    }
};
?>

<div class="mx-auto max-w-lg space-y-8">
    <div class="flex items-center justify-between">
        <flux:heading size="lg">Backup Disks</flux:heading>
        <flux:button :href="route('backup-disks.create')" variant="primary" wire:navigate>Create Backup Disk</flux:button>
    </div>

    @if ($this->backupDisks->isNotEmpty())
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Driver</flux:table.column>
                <flux:table.column>Created By</flux:table.column>
                <flux:table.column>Created At</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->backupDisks as $disk)
                    <flux:table.row :key="$disk->id">
                        <flux:table.cell>{{ $disk->name }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:text class="uppercase font-semibold text-xs">
                                {{ $disk->driver }}
                            </flux:text>
                        </flux:table.cell>
                        <flux:table.cell>{{ $disk->creator?->name ?? 'Unknown' }}</flux:table.cell>
                        <flux:table.cell>{{ $disk->created_at->diffForHumans() }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button
                                variant="ghost"
                                :href="route('backup-disks.edit', $disk->id)"
                                wire:navigate
                            >
                                Edit
                            </flux:button>
                            <flux:button
                                variant="ghost"
                                wire:confirm="Are you sure you want to delete this backup disk?"
                                wire:click="delete({{ $disk->id }})"
                            >
                                Delete
                            </flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    @endif
</div>
