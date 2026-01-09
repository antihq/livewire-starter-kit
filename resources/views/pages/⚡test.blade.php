<?php

use Livewire\Component;

new class extends Component {
    public string $name = '';

    public string $variant = 'marble';

    public int $size = 80;

    public bool $square = false;

    public function mount(): void
    {
        $this->name = 'Oliver';
    }
};
?>

<x-slot name="header">
    <flux:heading>Boring Avatars Showcase</flux:heading>
</x-slot>

<div class="space-y-12">
    <flux:card>
        <div class="flex flex-wrap items-center gap-6">
            <flux:field label="Name">
                <flux:input wire:model.live="name" placeholder="Enter name..." />
            </flux:field>

            <flux:field label="Variant">
                <flux:select wire:model.live="variant">
                    <flux:select.option value="bauhaus">Bauhaus</flux:select.option>
                    <flux:select.option value="beam">Beam</flux:select.option>
                    <flux:select.option value="marble">Marble</flux:select.option>
                    <flux:select.option value="pixel">Pixel</flux:select.option>
                    <flux:select.option value="ring">Ring</flux:select.option>
                    <flux:select.option value="sunset">Sunset</flux:select.option>
                </flux:select>
            </flux:field>

            <flux:field label="Size">
                <flux:input type="number" wire:model.live="size" min="32" max="200" />
            </flux:field>

            <flux:checkbox label="Square" wire:model.live="square" />
        </div>
    </flux:card>

    <flux:card class="flex min-h-[300px] items-center justify-center p-12">
        <x-boring-avatar
            :name="$name"
            :variant="$variant"
            :size="$size"
            :square="$square"
            class="transition-transform hover:scale-105"
        />
    </flux:card>

    <flux:heading size="lg">All Variants</flux:heading>
    <flux:card>
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-6">
            @foreach (['bauhaus', 'beam', 'marble', 'pixel', 'ring', 'sunset'] as $v)
                <div class="flex flex-col items-center space-y-2">
                    <x-boring-avatar name="{{ $name }}" :variant="$v" :size="64" />
                    <flux:text>
                        {{ $v }}
                    </flux:text>
                </div>
            @endforeach
        </div>
    </flux:card>

    <flux:heading size="lg">Different Names</flux:heading>
    <flux:card>
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 md:grid-cols-8">
            @foreach (['Alice', 'Bob', 'Charlie', 'Diana', 'Eve', 'Frank', 'Grace', 'Henry'] as $n)
                <div class="flex flex-col items-center space-y-2">
                    <x-boring-avatar :name="$n" :variant="$variant" :size="48" />
                    <flux:text>
                        {{ $n }}
                    </flux:text>
                </div>
            @endforeach
        </div>
    </flux:card>

    <flux:heading size="lg">Size Examples</flux:heading>
    <flux:card>
        <div class="flex flex-wrap items-end gap-4">
            <div class="flex flex-col items-center space-y-2">
                <x-boring-avatar :name="$name" :variant="$variant" :size="32" />
                <flux:text>32px</flux:text>
            </div>
            <div class="flex flex-col items-center space-y-2">
                <x-boring-avatar :name="$name" :variant="$variant" :size="48" />
                <flux:text>48px</flux:text>
            </div>
            <div class="flex flex-col items-center space-y-2">
                <x-boring-avatar :name="$name" :variant="$variant" :size="64" />
                <flux:text>64px</flux:text>
            </div>
            <div class="flex flex-col items-center space-y-2">
                <x-boring-avatar :name="$name" :variant="$variant" :size="96" />
                <flux:text>96px</flux:text>
            </div>
            <div class="flex flex-col items-center space-y-2">
                <x-boring-avatar :name="$name" :variant="$variant" :size="128" />
                <flux:text>128px</flux:text>
            </div>
        </div>
    </flux:card>
</div>
