<?php

use App\Models\Server;
use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component
{
    public Server $server;

    #[Validate('required|string|max:255')]
    public string $hostname = '';

    #[Validate('required|string|in:8.1,8.2,8.3,8.4,8.5')]
    public string $phpVersion = '8.5';

    #[Validate('required|string|in:generic,laravel,static')]
    public string $siteType = 'laravel';

    public bool $zeroDowntimeDeployments = false;

    #[Validate('required|string|max:255')]
    public string $webFolder = '/public';

    #[Validate('required|string|max:255')]
    public string $repositoryUrl = '';

    #[Validate('required|string|max:255')]
    public string $repositoryBranch = 'main';

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

    public function mount(): void
    {
        $this->authorize('create', Site::class);
        $this->authorize('view', $this->server);
    }

    public function create(): void
    {
        $this->validate();

        $this->team->sites()->create([
            'hostname' => $this->hostname,
            'server_id' => $this->server->id,
            'php_version' => $this->phpVersion,
            'site_type' => $this->siteType,
            'zero_downtime_deployments' => $this->zeroDowntimeDeployments,
            'web_folder' => $this->webFolder,
            'repository_url' => $this->repositoryUrl,
            'repository_branch' => $this->repositoryBranch,
            'creator_id' => $this->user->id,
        ]);

        $this->redirectRoute('servers.show', $this->server->id, navigate: true);
    }
};
?>

<div class="mx-auto max-w-lg">
    <form wire:submit="create" class="space-y-8">
        <flux:heading size="lg">Add Site to {{ $server->name }}</flux:heading>

        <flux:input wire:model="hostname" label="Hostname" placeholder="e.g., app.example.com" required />

        <flux:select wire:model="phpVersion" label="PHP Version" required>
            <flux:select.option value="8.1">8.1</flux:select.option>
            <flux:select.option value="8.2">8.2</flux:select.option>
            <flux:select.option value="8.3">8.3</flux:select.option>
            <flux:select.option value="8.4">8.4</flux:select.option>
            <flux:select.option value="8.5">8.5</flux:select.option>
        </flux:select>

        <flux:radio.group wire:model="siteType" label="Site Type">
            <flux:radio value="generic" label="Generic" />
            <flux:radio value="laravel" label="Laravel" />
            <flux:radio value="static" label="Static" />
        </flux:radio.group>

        <flux:checkbox
            wire:model="zeroDowntimeDeployments"
            label="Enable zero downtime deployments"
        />

        <flux:input wire:model="webFolder" label="Web Folder" placeholder="e.g., /public" required />

        <flux:input
            wire:model="repositoryUrl"
            label="Repository URL"
            placeholder="e.g., https://github.com/user/repo.git"
            required
        />

        <flux:input
            wire:model="repositoryBranch"
            label="Repository Branch"
            placeholder="e.g., main"
            required
        />

        <div class="flex gap-3">
            <flux:spacer />
            <flux:button variant="ghost" :href="route('servers.show', $server->id)" wire:navigate>Cancel</flux:button>
            <flux:button variant="primary" type="submit">Create Site</flux:button>
        </div>
    </form>
</div>
