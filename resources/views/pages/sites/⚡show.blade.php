<?php

use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public Site $site;

    #[Computed]
    public function user(): User
    {
        return Auth::user();
    }

    public function delete(): void
    {
        $this->site->delete();

        $this->redirectRoute('servers.sites.index', $this->site->server_id, navigate: true);
    }
};
?>

<div class="mx-auto max-w-lg space-y-8">
    <div class="flex items-center justify-between">
        <flux:heading size="lg">{{ $site->hostname }}</flux:heading>
        <div class="flex gap-2">
            <flux:button
                variant="danger"
                wire:confirm="Are you sure you want to delete this site?"
                wire:click="delete"
            >
                Delete
            </flux:button>
        </div>
    </div>

    <flux:heading size="md">Site Details</flux:heading>

    <div class="space-y-4">
        <div>
            <flux:text class="text-sm font-medium text-zinc-500">Server</flux:text>
            <flux:text>{{ $site->server?->name ?? 'Unknown' }}</flux:text>
        </div>

        <div>
            <flux:text class="text-sm font-medium text-zinc-500">PHP Version</flux:text>
            <flux:text>{{ $site->php_version }}</flux:text>
        </div>

        <div>
            <flux:text class="text-sm font-medium text-zinc-500">Site Type</flux:text>
            <flux:text>{{ ucfirst($site->site_type) }}</flux:text>
        </div>

        <div>
            <flux:text class="text-sm font-medium text-zinc-500">Zero Downtime Deployments</flux:text>
            <flux:text>{{ $site->zero_downtime_deployments ? 'Enabled' : 'Disabled' }}</flux:text>
        </div>

        <div>
            <flux:text class="text-sm font-medium text-zinc-500">Web Folder</flux:text>
            <flux:text class="font-mono">{{ $site->web_folder }}</flux:text>
        </div>

        <div>
            <flux:text class="text-sm font-medium text-zinc-500">Repository URL</flux:text>
            <flux:text class="font-mono text-xs max-w-md truncate block">{{ $site->repository_url }}</flux:text>
        </div>

        <div>
            <flux:text class="text-sm font-medium text-zinc-500">Repository Branch</flux:text>
            <flux:text>{{ $site->repository_branch }}</flux:text>
        </div>

        <div>
            <flux:text class="text-sm font-medium text-zinc-500">Created By</flux:text>
            <flux:text>{{ $site->creator?->name ?? 'Unknown' }}</flux:text>
        </div>

        <div>
            <flux:text class="text-sm font-medium text-zinc-500">Created At</flux:text>
            <flux:text>{{ $site->created_at->diffForHumans() }}</flux:text>
        </div>
    </div>

    <flux:button variant="ghost" :href="route('servers.sites.index', $site->server_id)" wire:navigate>Back to Sites</flux:button>
</div>
