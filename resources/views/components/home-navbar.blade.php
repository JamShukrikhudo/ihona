@php
    $settings = app(\App\Settings\GeneralSettings::class);
    $user = auth()->user();
    // The panel, not the role: a super_admin has no /super_admin to land on.
    $role = $user?->getRoleNames()->first() ?? 'user';
    $panel = \App\Http\Middleware\RoleBasedRedirect::PANELS[$role] ?? $role;
    $dashboardUrl = '/' . $panel;
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
                    {{ __(':role dashboard', ['role' => ucfirst($panel)]) }}
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
                    class="inline-flex rounded-sheet border border-sheet-300 bg-sheet-000 px-4 py-2 text-body-s font-semibold text-ink-900 transition-colors duration-[160ms] hover:border-ink-900">
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

        // Hover is handled in CSS. Everything else runs through aria-expanded,
        // which is the only state the stylesheet reads. Revealing on
        // :focus-within as well looked equivalent and was not: Escape returns
        // focus to the toggle, the toggle is inside the group, and the submenu
        // sprang straight back open.
        (function () {
            var toggles = document.querySelectorAll('[data-submenu-toggle]');

            function close(toggle) {
                toggle.setAttribute('aria-expanded', 'false');
            }

            function open(toggle) {
                toggle.setAttribute('aria-expanded', 'true');
            }

            Array.prototype.forEach.call(toggles, function (toggle) {
                var item = toggle.closest('.group');

                if (! item) return;

                // Tabbing onto the parent link reveals it, the same as hovering.
                // Not when the toggle itself takes focus: focus lands before
                // the click fires, so opening here made the click that
                // followed read as a close and the button did nothing.
                item.addEventListener('focusin', function (event) {
                    if (event.target !== toggle) open(toggle);
                });

                toggle.addEventListener('click', function () {
                    toggle.setAttribute(
                        'aria-expanded',
                        toggle.getAttribute('aria-expanded') === 'true' ? 'false' : 'true'
                    );
                });

                item.addEventListener('keydown', function (event) {
                    if (event.key !== 'Escape') return;

                    close(toggle);
                    toggle.focus();
                });

                // Tabbing out of the last child closes it behind you.
                item.addEventListener('focusout', function (event) {
                    if (! item.contains(event.relatedTarget)) close(toggle);
                });
            });

            document.addEventListener('click', function (event) {
                Array.prototype.forEach.call(
                    document.querySelectorAll('[data-submenu-toggle][aria-expanded="true"]'),
                    function (toggle) {
                        var item = toggle.closest('.group');

                        if (item && ! item.contains(event.target)) close(toggle);
                    }
                );
            });
        })();
    </script>
@endpush
