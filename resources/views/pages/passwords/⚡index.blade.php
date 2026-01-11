<?php

use App\Models\Password;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public function mount()
    {
        $this->generatePassword();
    }

    #[Computed]
    public function user()
    {
        return Auth::user();
    }

    #[Computed]
    public function passwords()
    {
        return Password::where('team_id', $this->user->current_team_id)
            ->latest()
            ->get();
    }

    public $name = '';

    public $username = '';

    public $password = '';

    public function generatePassword(): void
    {
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $alphanumeric = 'abcdefghijklmnopqrstuvwxyz0123456789';

        $part1 = substr(str_shuffle(str_repeat($lowercase, 3)), 0, 6);
        $part2 = substr(str_shuffle(str_repeat($alphanumeric, 3)), 0, 6);
        $part3 = ucfirst(substr(str_shuffle(str_repeat($lowercase, 3)), 0, 6));

        $this->password = sprintf('%s-%s-%s', $part1, $part2, $part3);
    }

    public function create()
    {
        $this->authorize('create', Password::class);

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $this->user->currentTeam->passwords()->create([
            'name' => $this->pull('name'),
            'username' => $this->pull('username'),
            'password' => $this->pull('password'),
        ]);

        $this->modal('create-password')->close();
    }

    public function delete(Password $password)
    {
        $this->authorize('delete', $password);

        $password->delete();
    }
};
?>

<section class="mx-auto max-w-6xl space-y-8">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Passwords</flux:heading>

        @if ($this->passwords->isNotEmpty())
            <flux:modal.trigger name="create-password">
                <flux:button variant="primary">Create password</flux:button>
            </flux:modal.trigger>
        @endif
    </div>

    @if ($this->passwords->isNotEmpty())
        <ul role="list" class="divide-y divide-zinc-200 dark:divide-white/10 overflow-hidden">
            @foreach ($this->passwords as $password)
                <livewire:passwords.item :$password key="password-{{ $password->id }}" />
            @endforeach
        </ul>
    @else
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center">
                <flux:icon.key variant="outline" class="size-6 text-zinc-500 dark:text-zinc-400" />
            </div>
            <flux:heading class="mt-2">No passwords</flux:heading>
            <flux:text class="mt-1">Get started by creating a new password.</flux:text>
            <div class="mt-6">
                <flux:modal.trigger name="create-password">
                    <flux:button variant="primary">Create password</flux:button>
                </flux:modal.trigger>
            </div>
        </div>
    @endif

    <flux:modal name="create-password" class="w-full sm:max-w-lg">
        <form wire:submit="create" class="space-y-8">
            <div class="space-y-6">
                <div class="space-y-2">
                    <flux:heading size="lg">Create password</flux:heading>
                    <flux:text>Create a new password credential to store securely.</flux:text>
                </div>

                <flux:input wire:model="name" label="Name" type="text" required autofocus />

                <flux:input wire:model="username" label="Username" type="text" required />

                <flux:input wire:model="password" label="Password" type="text" required copyable>
                    <x-slot name="iconTrailing">
                        <flux:button size="sm" variant="subtle" icon="sparkles" class="-mr-1" wire:click="generatePassword" />
                    </x-slot>
                </flux:input>
            </div>

            <div
                class="flex flex-col-reverse items-center justify-end gap-3 *:w-full sm:flex-row sm:*:w-auto"
            >
                <flux:modal.close>
                    <flux:button variant="ghost" class="w-full sm:w-auto">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">Save</flux:button>
            </div>
        </form>
    </flux:modal>
</section>
