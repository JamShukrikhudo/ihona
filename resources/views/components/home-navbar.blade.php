@php
    $settings = app(\App\Settings\GeneralSettings::class);
    $user = auth()->user();
    $role = $user?->getRoleNames()->first() ?? 'user';
    $dashboardUrl = $role === 'admin' ? '/admin' : '/' . $role;
@endphp

<nav class="border-b border-sheet-300 bg-sheet-000">
    <div class="mx-auto flex max-w-(--breakpoint-xl) flex-wrap items-center justify-between gap-gutter p-4">
        <a href="{{ url('/') }}" class="flex items-center gap-3 rtl:space-x-reverse">
            <img src="{{ asset('build/images/logo.png') }}" class="h-8" alt="" />
            <span class="font-display text-h5 font-bold tracking-tight text-ink-900">
                {{ $settings->site_name }}
            </span>
        </a>

        {{-- buildMenu() emits its own <ul>; wrapping it in another produces
             <ul><ul><li>, which is invalid and swallows the wrapper's classes. --}}
        <div class="hidden w-full items-center justify-between text-body-s font-medium lg:flex lg:w-auto"
             id="navbar-cta">
            {!! app(App\Services\MenuService::class)->buildMenu() !!}
        </div>

        <div class="flex items-center gap-3 rtl:space-x-reverse">
            <x-theme-switch />

            @auth
                <a href="{{ $dashboardUrl }}"
                    class="rounded-tag px-3 py-2 text-body-s font-medium text-ink-700 transition-colors duration-[160ms] hover:text-survey-600">
                    {{ __(':role dashboard', ['role' => ucfirst($role)]) }}
                </a>
            @else
                <a href="{{ Route::has('login') ? route('login') : url('/app/login') }}"
                    class="rounded-tag px-3 py-2 text-body-s font-medium text-ink-700 transition-colors duration-[160ms] hover:text-survey-600">
                    {{ __('Log in') }}
                </a>
                {{-- Never hidden: the mobile menu carries only CMS items, so
                     hiding this below sm left a phone visitor with no route to
                     registration at all. --}}
                <a href="{{ Route::has('register') ? route('register') : url('/app/register') }}"
                    class="inline-flex rounded-sheet bg-action px-4 py-2 text-body-s font-semibold text-white shadow-lift-1 transition-colors duration-[160ms] hover:bg-action-hover">
                    {{ __('Register') }}
                </a>
            @endauth

            <button id="menuToggleButton" type="button"
                class="inline-flex h-11 w-11 items-center justify-center rounded-tag text-ink-500 transition-colors duration-[160ms] hover:bg-sheet-200 hover:text-ink-900 lg:hidden"
                aria-controls="menuToggle" aria-expanded="false">
                <span class="sr-only">{{ __('Open main menu') }}</span>
                <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 17 14">
                    <path stroke="currentColor" stroke-linecap="square" stroke-width="1.5" d="M1 1h15M1 7h15M1 13h15" />
                </svg>
            </button>
        </div>
    </div>

    <div class="hidden border-t border-sheet-300 p-4 text-body-s font-medium lg:hidden" id="menuToggle">
        {!! app(App\Services\MenuService::class)->buildMenu() !!}
    </div>
</nav>

@push('scripts')
    <script>
        (function () {
            var button = document.getElementById('menuToggleButton');
            var menu = document.getElementById('menuToggle');

            if (! button || ! menu) return;

            button.addEventListener('click', function () {
                var open = menu.classList.toggle('hidden') === false;
                button.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
        })();
    </script>
@endpush
