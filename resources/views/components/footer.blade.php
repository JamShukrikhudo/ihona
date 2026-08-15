@php
    $settings = app(\App\Settings\GeneralSettings::class);

    // A drawing sheet ends in a title block: who drew it, when, and under what
    // reference. The footer carries the same fields, because on a property site
    // those are the trust content — not decoration around a list of links.
    $titleBlock = array_filter([
        __('Office') => $settings->site_address,
        __('Telephone') => $settings->site_phone,
        __('Email') => $settings->site_email,
        __('Country') => $settings->site_country,
    ]);

    $social = array_filter([
        'Facebook' => $settings->facebook_url,
        'X' => $settings->twitter_url,
        'YouTube' => $settings->youtube_url,
        'GitHub' => $settings->github_url,
    ]);
@endphp

<footer class="border-t border-sheet-300 bg-sheet-000">
    <div class="mx-auto max-w-(--breakpoint-xl) px-4 py-band">
        <div class="border border-ink-900">
            <div class="border-b border-sheet-300 p-4">
                <div class="font-mono text-annotation uppercase tracking-[0.09em] text-ink-400">
                    {{ __('Issued by') }}
                </div>
                <div class="mt-1 font-display text-h5 font-bold tracking-tight text-ink-900">
                    {{ $settings->site_name }}
                </div>
            </div>

            {{-- Hairlines are drawn per cell, not by showing a parent
                 background through gap-px: most of these settings are null by
                 default, so a part-filled grid would paint the empty columns as
                 a solid slab. --}}
            @if ($titleBlock)
                <dl class="grid grid-cols-2 md:grid-cols-4">
                    @foreach ($titleBlock as $label => $value)
                        <div class="border-t border-sheet-300 p-4">
                            <dt class="font-mono text-[9.5px] uppercase tracking-[0.1em] text-ink-400">
                                {{ $label }}
                            </dt>
                            <dd class="mt-1 font-mono text-caption text-ink-900">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            @endif
        </div>

        <div class="mt-6 flex flex-wrap items-center justify-between gap-4">
            <p class="text-caption text-ink-400">{{ $settings->footer_copyright }}</p>

            <div class="flex flex-wrap items-center gap-4">
                @foreach ($social as $name => $url)
                    <a href="{{ $url }}" rel="noopener noreferrer" target="_blank"
                       class="font-mono text-annotation uppercase tracking-[0.09em] text-ink-400 transition-colors duration-[160ms] hover:text-ink-900">
                        {{ $name }}
                    </a>
                @endforeach

                <a href="{{ route('termsandconditions') }}"
                   class="font-mono text-annotation uppercase tracking-[0.09em] text-ink-400 transition-colors duration-[160ms] hover:text-ink-900">
                    {{ __('Terms') }}
                </a>
                <a href="{{ route('privacypolicy') }}"
                   class="font-mono text-annotation uppercase tracking-[0.09em] text-ink-400 transition-colors duration-[160ms] hover:text-ink-900">
                    {{ __('Privacy') }}
                </a>
            </div>
        </div>
    </div>
</footer>
