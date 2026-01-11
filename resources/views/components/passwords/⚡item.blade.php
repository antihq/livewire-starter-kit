<?php

use App\Models\Password;
use Livewire\Component;

new class extends Component
{
    public Password $password;

    public bool $isEditing = false;

    public string $editName = '';

    public string $editUsername = '';

    public string $editPassword = '';

    public function enterEditMode(): void
    {
        $this->isEditing = true;
        $this->editName = $this->password->name;
        $this->editUsername = $this->password->username;
        $this->editPassword = $this->password->password;
    }

    public function cancelEdit(): void
    {
        $this->isEditing = false;
        $this->reset(['editName', 'editUsername', 'editPassword']);
    }

    public function save(): void
    {
        $this->authorize('update', $this->password);

        $this->validate([
            'editName' => ['required', 'string', 'max:255'],
            'editUsername' => ['required', 'string', 'max:255'],
            'editPassword' => ['required', 'string'],
        ]);

        $this->password->update([
            'name' => $this->editName,
            'username' => $this->editUsername,
            'password' => $this->editPassword,
        ]);

        $this->isEditing = false;
    }
};
?>

<li wire:key="password-{{ $password->id }}" class="relative flex justify-between gap-x-6 py-5">
    <div class="flex min-w-0 gap-x-4">
        <div class="min-w-0 flex-auto">
            <flux:heading>
                <flux:modal.trigger name="view-password-{{ $password->id }}">
                    <span class="absolute inset-x-0 -top-px bottom-0"></span>
                    {{ $password->name }}
                </flux:modal.trigger>
            </flux:heading>
            <flux:text size="sm">
                {{ $password->username }}
            </flux:text>
            <flux:modal name="view-password-{{ $password->id }}" class="w-full sm:max-w-lg">
                <form wire:submit="save" class="space-y-8">
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <flux:heading size="lg">
                                    {{ $isEditing ? 'Edit password' : 'View password' }}
                                </flux:heading>
                            </div>
                            <flux:text>
                                {{ $isEditing ? 'Update your password details below.' : 'View your password details below.' }}
                            </flux:text>
                        </div>

                        @if ($isEditing)
                            <flux:input wire:model="editName" label="Name" type="text" required autofocus />

                            <flux:input wire:model="editUsername" label="Username" type="text" required />

                            <flux:input wire:model="editPassword" label="Password" type="text" required />
                        @else
                            <flux:input :value="$password->name" label="Name" readonly variant="filled" />

                            <flux:input :value="$password->username" label="Username" readonly variant="filled" copyable />

                            <flux:input :value="$password->password" label="Password" type="password" readonly variant="filled" copyable viewable />
                        @endif
                    </div>

                    <div
                        class="flex flex-col-reverse items-center justify-end gap-3 *:w-full sm:flex-row sm:*:w-auto"
                    >
                        @if ($isEditing)
                            <flux:button wire:click="cancelEdit" variant="ghost" class="w-full sm:w-auto">Cancel</flux:button>
                            <flux:button type="submit" variant="primary">Save</flux:button>
                        @else
                            <flux:modal.close>
                                <flux:button variant="ghost" class="w-full sm:w-auto">Close</flux:button>
                            </flux:modal.close>
                            <flux:button wire:click="enterEditMode" variant="filled">Edit</flux:button>
                        @endif
                    </div>
                </form>
            </flux:modal>
        </div>
    </div>
    <div class="flex shrink-0 items-center gap-x-4">
        <flux:dropdown align="end">
            <flux:button icon="ellipsis-horizontal" variant="ghost" square />

            <flux:menu>
                <flux:menu.item variant="danger" icon="trash" wire:click="$parent.delete({{ $password->id }})">
                    Delete
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </div>
</li>
