@props([
    'eyebrow' => null,
    'title',
    'lede' => null,
    'updated' => null,
])

{{--
    One treatment for every page a visitor reads rather than operates: about,
    services, the legal pages, a news article.

    The measure is the point. A paragraph running the full width of a 1440px
    sheet is skimmed and abandoned, so the column stops at roughly 65
    characters whatever the viewport does.
--}}
<article class="mx-auto max-w-(--breakpoint-xl) px-4 py-band md:px-margin">
    <header class="max-w-reading">
        @if ($eyebrow)
            <p class="font-mono text-annotation uppercase text-ink-400">{{ $eyebrow }}</p>
        @endif

        <h1 class="mt-3 font-display text-h2 font-bold tracking-tight text-ink-900">{{ $title }}</h1>

        @if ($lede)
            <p class="mt-4 text-body-l text-ink-500">{{ $lede }}</p>
        @endif

        @if ($updated)
            <p class="mt-4 font-mono text-annotation uppercase text-ink-400">
                {{ __('Last updated :date', ['date' => $updated]) }}
            </p>
        @endif
    </header>

    {{-- The type scale rather than default prose: headings take the display
         face, links the reference blue, and every list and rule comes from the
         same tokens as the rest of the site. --}}
    <div class="mt-8 max-w-reading space-y-5 text-body text-ink-700
                [&_h2]:mt-10 [&_h2]:font-display [&_h2]:text-h4 [&_h2]:font-bold [&_h2]:tracking-tight [&_h2]:text-ink-900
                [&_h3]:mt-8 [&_h3]:font-display [&_h3]:text-h5 [&_h3]:font-bold [&_h3]:text-ink-900
                [&_a]:text-draft-500 [&_a]:underline [&_a]:underline-offset-[3px]
                [&_a:hover]:text-ink-900
                [&_ul]:list-disc [&_ul]:pl-5 [&_ul]:space-y-1.5
                [&_ol]:list-decimal [&_ol]:pl-5 [&_ol]:space-y-1.5
                [&_strong]:font-semibold [&_strong]:text-ink-900
                [&_hr]:border-sheet-300">
        {{ $slot }}
    </div>
</article>
