<x-filament-panels::page>
    <div class="mx-auto w-full max-w-4xl space-y-6">
        <div class="rounded-2xl bg-gradient-to-br from-primary-600 to-primary-800 p-6 text-white shadow-sm sm:p-8">
            <p class="text-sm font-medium text-white/75">A quick start for your workspace</p>
            <h1 class="mt-2 text-2xl font-semibold tracking-tight sm:text-3xl">Set up your account with confidence</h1>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-white/80">Complete the essentials now, then return here whenever you need to update team connections or service credentials.</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-4 text-sm dark:border-white/10 dark:bg-white/5">
            <h2 class="font-semibold">OAuth sign-in availability</h2>
            <div class="mt-3 grid gap-2 sm:grid-cols-4">
                @foreach ($this->oauthProviders() as $provider => $configured)
                    <div wire:key="oauth-{{ str($provider)->slug() }}" class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
                        <x-filament::icon :icon="$configured ? 'heroicon-o-check-circle' : 'heroicon-o-minus-circle'" class="h-4 w-4 {{ $configured ? 'text-success-500' : 'text-gray-400' }}" />
                        <span>{{ $provider }}</span>
                    </div>
                @endforeach
            </div>
            <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">OAuth client IDs and secrets are application-level settings. An administrator can configure them in the deployment environment; you do not need to paste them into this form.</p>
        </div>

        <form wire:submit="save">
            {{ $this->form }}
            <div class="mt-6 flex justify-end">
                <x-filament::button type="submit" icon="heroicon-m-check">Save setup</x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>
