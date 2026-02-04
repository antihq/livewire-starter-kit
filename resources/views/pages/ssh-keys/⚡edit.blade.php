<?php

use App\Models\SshKey;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public SshKey $sshKey;

    public array $serverIds = [];

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
    public function servers()
    {
        return $this->team->servers()->latest()->get();
    }

    public function mount(): void
    {
        $this->authorize('update', $this->sshKey);

        $this->serverIds = $this->sshKey->servers->pluck('id')->toArray();
    }

    public function update(): void
    {
        $this->sshKey->servers()->sync($this->serverIds);

        $this->redirectRoute('ssh-keys.index', navigate: true);
    }
};
?>

<div class="mx-auto max-w-lg">
    <form wire:submit="update" class="space-y-8">
        <flux:heading size="lg">Edit Servers</flux:heading>

        <div class="space-y-4">
            <flux:heading size="md">SSH Key</flux:heading>
            <flux:input label="Name" value="{{ $sshKey->name }}" readonly />
            <flux:input
                label="Public Key"
                type="textarea"
                value="{{ $sshKey->public_key }}"
                readonly
            />
        </div>

        @if ($this->servers->isNotEmpty())
            <div class="space-y-4">
                <flux:heading size="md">Servers</flux:heading>
                @foreach ($this->servers as $server)
                    <flux:checkbox
                        wire:model.live="serverIds"
                        :value="$server->id"
                        label="{{ $server->name }}"
                    />
                @endforeach
            </div>
        @else
            <flux:text class="text-gray-500">No servers available. Create a server first.</flux:text>
        @endif

        <div class="flex gap-3">
            <flux:spacer />
            <flux:button variant="ghost" :href="route('ssh-keys.index')" wire:navigate>Cancel</flux:button>
            <flux:button variant="primary" type="submit">Save Changes</flux:button>
        </div>
    </form>
</div>
