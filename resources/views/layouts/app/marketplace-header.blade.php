<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="dark antialiased lg:bg-zinc-100 dark:bg-zinc-900 dark:lg:bg-zinc-950"
>
    <head>
        @include('partials.head')
    </head>
    <body class="flex min-h-svh w-full flex-col bg-white lg:bg-zinc-100 dark:bg-zinc-900 dark:lg:bg-zinc-950">
        <flux:header>
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" />

            <flux:brand :href="route('marketplaces.show', $marketplace)" :name="$marketplace->name" />

            <flux:spacer />

            <flux:navbar class="me-4">
                <flux:navbar.item :href="route('marketplaces.listings.create', $marketplace)" wire:navigate>
                    Post a new listing
                </flux:navbar.item>
                @guest
                    <flux:navbar.item :href="route('marketplaces.join', ['marketplace' => $marketplace, 'register' => true])" wire:navigate>
                        Signup
                    </flux:navbar.item>
                    <flux:navbar.item :href="route('marketplaces.join', $marketplace)" wire:navigate>
                        Login
                    </flux:navbar.item>
                @else
                    <flux:navbar.item :href="route('marketplaces.conversations.index', $marketplace)" wire:navigate>
                        Inbox
                    </flux:navbar.item>
                @endguest
            </flux:navbar>

            @auth
                <livewire:profile-dropdown />
            @endauth
        </flux:header>

        <!-- Mobile Menu -->
        <flux:sidebar
            sticky
            collapsible="mobile"
            class="bg-zinc-50 shadow-xs ring-1 ring-zinc-950/5 lg:hidden dark:bg-zinc-900 dark:ring-white/10"
        >
            <flux:sidebar.header>
                <flux:spacer />
                <flux:sidebar.collapse
                    class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2"
                />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.item
                    :href="route('dashboard')"
                    :current="request()->routeIs('dashboard')"
                    wire:navigate
                >
                    Dashboard
                </flux:sidebar.item>
            </flux:sidebar.nav>
        </flux:sidebar>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
