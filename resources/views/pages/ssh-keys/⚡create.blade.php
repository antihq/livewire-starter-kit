<?php

use App\Models\SshKey;
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
    public string $public_key = '';

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
        $this->authorize('create', SshKey::class);
    }

    public function create(): void
    {
        $this->validate();

        $this->team->sshKeys()->create([
            'name' => $this->name,
            'public_key' => $this->public_key,
            'creator_id' => $this->user->id,
        ]);

        $this->redirectRoute('ssh-keys.index', navigate: true);
    }
};
?>

<div class="mx-auto max-w-lg">
    <form wire:submit="create" class="space-y-8">
        <flux:heading size="lg">Create SSH Key</flux:heading>

        <flux:input wire:model="name" label="Name" placeholder="e.g., MacBook Pro" required />

        <flux:input
            wire:model="public_key"
            label="Public Key"
            type="textarea"
            placeholder="ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAI..."
            required
        />

        <div class="flex gap-3">
            <flux:spacer />
            <flux:button variant="ghost" :href="route('ssh-keys.index')" wire:navigate>Cancel</flux:button>
            <flux:button variant="primary" type="submit">Create SSH Key</flux:button>
        </div>
    </form>
</div>
