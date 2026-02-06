<?php

use App\Models\Server;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string')]
    public string $public_ip = '';

    public array $sshKeyIds = [];

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
        $this->authorize('create', Server::class);
    }

    public function create(): void
    {
        $this->validate();

        $server = $this->team->servers()->create([
            'name' => $this->name,
            'public_ip' => $this->public_ip,
            'creator_id' => $this->user->id,
        ]);

        if (! empty($this->sshKeyIds)) {
            $server->sshKeys()->sync($this->sshKeyIds);
        }

        $this->redirectRoute('servers.show', $server, navigate: true);
    }
};
?>

<div class="mx-auto max-w-lg">
    <form wire:submit="create" class="space-y-8">
        <flux:heading size="lg">Create Server</flux:heading>

        <flux:input wire:model="name" label="Name" placeholder="e.g., Production Web Server" required />

        <flux:input
            wire:model="public_ip"
            label="Public IP"
            placeholder="e.g., 192.168.1.100"
            required
        />

        @if ($this->sshKeys->isNotEmpty())
            <div class="space-y-4">
                <flux:heading size="md">SSH Keys (Optional)</flux:heading>
                @foreach ($this->sshKeys as $sshKey)
                    <flux:checkbox
                        wire:model.live="sshKeyIds"
                        :value="$sshKey->id"
                        label="{{ $sshKey->name }}"
                    />
                @endforeach
            </div>
        @endif

        <div class="flex gap-3">
            <flux:spacer />
            <flux:button variant="ghost" :href="route('servers.index')" wire:navigate>Cancel</flux:button>
            <flux:button variant="primary" type="submit">Create Server</flux:button>
        </div>
    </form>
</div>
