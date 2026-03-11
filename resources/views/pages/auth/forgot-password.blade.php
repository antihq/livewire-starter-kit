<x-layouts::auth>
    <div class="flex flex-col gap-8">
        <x-auth-header title="Reset your password" description="Enter your email and we’ll send you a link to reset your password." />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-8">
            @csrf

            <!-- Email Address -->
            <flux:input
                name="email"
                label="Email"
                type="email"
                required
                autofocus
            />

            <flux:button variant="primary" type="submit" class="w-full" data-test="email-password-reset-link-button">
                Reset password
            </flux:button>
        </form>

        <flux:text>
            Don’t have an account?
            <flux:link :href="route('register')" wire:navigate>Sign up</flux:link>
        </flux:text>
    </div>
</x-layouts::auth>
