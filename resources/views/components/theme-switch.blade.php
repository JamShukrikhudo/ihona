{{--
    Vellum / Night. Two states, no words: a sun and a moon are a control every
    reader already knows, which is the one case the system allows an icon to
    stand without a visible label. The name lives in aria-label and title.

    Stamping `data-theme` on the document flips `color-scheme`, and every colour
    token is a light-dark() pair, so the whole site changes from this attribute
    alone. The head of the layout replays a stored choice before first paint.
--}}
<div class="inline-flex overflow-hidden rounded-sheet border border-sheet-300 bg-sheet-000"
     role="group"
     aria-label="{{ __('Sheet theme') }}"
     data-theme-switch>
    <button type="button"
            data-theme-set="light"
            aria-pressed="false"
            aria-label="{{ __('Vellum — the daylight sheet') }}"
            title="{{ __('Vellum') }}"
            class="cursor-pointer px-3 py-[7px] text-ink-400 transition-colors duration-[160ms]
                   hover:text-ink-900 aria-pressed:bg-ink-900 aria-pressed:text-sheet-000">
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor"
             stroke-width="1.5" stroke-linecap="square" stroke-linejoin="miter" aria-hidden="true">
            <path d="M9 9h6v6H9z"/>
            <path d="M12 2v3M12 19v3M2 12h3M19 12h3M4.9 4.9l2.2 2.2M16.9 16.9l2.2 2.2M4.9 19.1l2.2-2.2M16.9 7.1l2.2-2.2"/>
        </svg>
    </button>
    <button type="button"
            data-theme-set="dark"
            aria-pressed="false"
            aria-label="{{ __('Night — the inverted sheet') }}"
            title="{{ __('Night') }}"
            class="cursor-pointer border-l border-sheet-300 px-3 py-[7px] text-ink-400
                   transition-colors duration-[160ms] hover:text-ink-900
                   aria-pressed:bg-ink-900 aria-pressed:text-sheet-000">
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor"
             stroke-width="1.5" stroke-linecap="square" stroke-linejoin="miter" aria-hidden="true">
            <path d="M21 12.8A9 9 0 1 1 11.2 3 7 7 0 0 0 21 12.8z"/>
        </svg>
    </button>
</div>

@once
    @push('scripts')
        <script>
            (function () {
                var media = window.matchMedia('(prefers-color-scheme: dark)');
                var buttons = document.querySelectorAll('[data-theme-set]');

                function stored() {
                    try {
                        var t = localStorage.getItem('theme');
                        return t === 'light' || t === 'dark' ? t : null;
                    } catch (e) {
                        return null;
                    }
                }

                function current() {
                    return stored() || (media.matches ? 'dark' : 'light');
                }

                function apply(theme, remember) {
                    document.documentElement.dataset.theme = theme;

                    if (remember) {
                        try { localStorage.setItem('theme', theme); } catch (e) {}
                    }

                    buttons.forEach(function (button) {
                        button.setAttribute(
                            'aria-pressed',
                            button.dataset.themeSet === theme ? 'true' : 'false'
                        );
                    });
                }

                apply(current(), false);

                buttons.forEach(function (button) {
                    button.addEventListener('click', function () {
                        apply(button.dataset.themeSet, true);
                    });
                });

                // Follow the operating system only until the reader chooses.
                media.addEventListener('change', function () {
                    if (! stored()) {
                        apply(media.matches ? 'dark' : 'light', false);
                    }
                });
            })();
        </script>
    @endpush
@endonce
