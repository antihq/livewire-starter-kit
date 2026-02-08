<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="dark antialiased lg:bg-zinc-100 dark:bg-zinc-900 dark:lg:bg-zinc-950"
>
    <head>
        @include('partials.head')
    </head>
    <body class="flex min-h-svh w-full flex-col bg-white lg:bg-zinc-100 dark:bg-zinc-900 dark:lg:bg-zinc-950">
        <livewire:marketplace-header />

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

        <flux:main class="flex flex-1 flex-col px-0! pt-0! pb-0! lg:px-2!">
            <div
                class="grow p-6 lg:rounded-lg lg:bg-white lg:p-10 lg:shadow-xs lg:ring-1 lg:ring-zinc-950/5 dark:lg:bg-zinc-900 dark:lg:ring-white/10"
            >
                {{ $slot }}
            </div>
        </flux:main>

        @persist('toast')
            <flux:toast position="bottom center" />
        @endpersist

        @fluxScripts
    </body>
</html>
