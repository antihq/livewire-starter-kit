<?php

use App\Models\SshKey;
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
    public function sshKeys()
    {
        return $this->team->sshKeys()->latest()->get();
    }

    public function mount(): void
    {
        $this->authorize('viewAny', SshKey::class);
    }

    public function delete(int $id): void
    {
        $sshKey = $this->team->sshKeys()->findOrFail($id);

        $this->authorize('delete', $sshKey);

        $sshKey->delete();
    }
};
?>

<div class="mx-auto max-w-lg space-y-8">
    <div class="flex items-center justify-between">
        <flux:heading size="lg">SSH Keys</flux:heading>
        <flux:button :href="route('ssh-keys.create')" variant="primary" wire:navigate>Create SSH Key</flux:button>
    </div>

    @if ($this->sshKeys->isNotEmpty())
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Public Key</flux:table.column>
                <flux:table.column>Created By</flux:table.column>
                <flux:table.column>Created At</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->sshKeys as $key)
                    <flux:table.row :key="$key->id">
                        <flux:table.cell>{{ $key->name }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:text class="max-w-md truncate font-mono text-xs">
                                {{ $key->public_key }}
                            </flux:text>
                        </flux:table.cell>
                        <flux:table.cell>{{ $key->creator?->name ?? 'Unknown' }}</flux:table.cell>
                        <flux:table.cell>{{ $key->created_at->diffForHumans() }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button
                                variant="ghost"
                                :href="route('ssh-keys.edit', $key->id)"
                                wire:navigate
                            >
                                Edit Servers
                            </flux:button>
                            <flux:button
                                variant="ghost"
                                wire:confirm="Are you sure you want to delete this SSH key?"
                                wire:click="delete({{ $key->id }})"
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
