<x-layouts::app.marketplace-header :title="$title ?? null" :marketplace="request()->marketplace">
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
</x-layouts::app.marketplace-header>
