<?php

use App\Models\Marketplace;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.marketplace')] class extends Component
{
    public Marketplace $marketplace;

    #[Computed]
    public function listings()
    {
        return $this->marketplace->listings;
    }
};
?>

<x-slot name="title">{{ $marketplace->name }}</x-slot>

<div class="max-w-sm mx-auto">
    <flux:heading level="1" size="xl">232 stays in Melbourne</flux:heading>
    <flux:text class="mt-0.5">Book your next stay at one of our properties.</flux:text>
    <flux:separator class="mt-4" />
    <div class="mt-8">
        <flux:input placeholder="Search" icon="magnifying-glass" />
        <flux:button icon="adjustments-vertical" class="mt-6 w-full">2 filters applied</flux:button>
    </div>
    <div class="mt-8">
        <flux:button.group>
            <flux:button class="w-full">Short by date</flux:button>
            <flux:button class="w-full">Short by price</flux:button>
        </flux:button.group>
        <div class="mt-6 space-y-4">
            <flux:card class="p-0! overflow-hidden">
                <img class="h-44 w-full object-cover" src="https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?q=80&w=2160&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D">
                <div class="pt-5 pb-4 px-4">
                    <div class="flex items-center gap-2">
                        <flux:heading level="1" size="xl">$540</flux:heading>
                        <flux:text>AUD</flux:text>
                    </div>
                    <flux:heading size="lg" class="mt-4">A Stylish Apt, 5 min walk to Queen Victoria Market</flux:heading>
                </div>
            </flux:card>
            <flux:card class="p-0! overflow-hidden">
                <img class="h-44 w-full object-cover" src="https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?q=80&w=2160&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D">
                <div class="pt-5 pb-4 px-4">
                    <div class="flex items-center gap-2">
                        <flux:heading level="1" size="xl">$540</flux:heading>
                        <flux:text>AUD</flux:text>
                    </div>
                    <flux:heading size="lg" class="mt-4">A Stylish Apt, 5 min walk to Queen Victoria Market</flux:heading>
                </div>
            </flux:card>
        </div>
        <div class="mt-6 pt-4">
            <div class="flex justify-between gap-4">
                <flux:button icon="arrow-left" />
                <flux:button icon="arrow-right" />
            </div>
        </div>
    </div>
</div>
