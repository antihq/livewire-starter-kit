<?php

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

    public function delete(): void
    {
        $this->server->delete();

        $this->redirectRoute('servers.index', navigate: true);
    }
};
?>

<div class="mx-auto max-w-lg space-y-8">
    <div class="flex items-center justify-between">
        <flux:heading size="lg">{{ $server->name }}</flux:heading>
        <div class="flex gap-2">
            <flux:button
                variant="danger"
                wire:confirm="Are you sure you want to delete this server?"
                wire:click="delete"
            >
                Delete
            </flux:button>
        </div>
    </div>

    <flux:heading size="md">Server Details</flux:heading>

    <div class="space-y-4">
        <div>
            <flux:text class="text-sm font-medium text-zinc-500">Public IP</flux:text>
            <flux:text class="font-mono">{{ $server->public_ip }}</flux:text>
        </div>

        <div>
            <flux:text class="text-sm font-medium text-zinc-500">Created By</flux:text>
            <flux:text>{{ $server->creator?->name ?? 'Unknown' }}</flux:text>
        </div>

        <div>
            <flux:text class="text-sm font-medium text-zinc-500">Created At</flux:text>
            <flux:text>{{ $server->created_at->diffForHumans() }}</flux:text>
        </div>
    </div>

    @if ($server->sshKeys->isNotEmpty())
        <flux:heading size="md">SSH Keys ({{ $server->sshKeys->count() }})</flux:heading>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Created By</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($server->sshKeys as $sshKey)
                    <flux:table.row :key="$sshKey->id">
                        <flux:table.cell>{{ $sshKey->name }}</flux:table.cell>
                        <flux:table.cell>{{ $sshKey->creator?->name ?? 'Unknown' }}</flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    @else
        <flux:text class="text-zinc-500">No SSH keys attached to this server.</flux:text>
    @endif

    <flux:button variant="ghost" :href="route('servers.index')" wire:navigate>Back to Servers</flux:button>
</div>
