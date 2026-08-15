@props([
    'band' => null,
    'score' => null,
])

@php
    // Statutory colours, set by the certificate itself. Recognising the exact
    // green of a band B is part of why the disclosure strip earns trust, so
    // these sit outside the palette and are never restyled.
    //
    // Written as whole class names on purpose: Tailwind scans this file as
    // text and drops any @theme variable no utility references, so an inline
    // `var(--color-epc-a)` would compile to nothing and paint white on white.
    $bands = [
        'A' => 'bg-epc-a text-white',
        'B' => 'bg-epc-b text-white',
        'C' => 'bg-epc-c text-[#121614]',
        'D' => 'bg-epc-d text-[#121614]',
        'E' => 'bg-epc-e text-[#121614]',
        'F' => 'bg-epc-f text-white',
        'G' => 'bg-epc-g text-white',
    ];

    $band = strtoupper(trim((string) $band));
    $tone = $bands[$band] ?? null;

    $label = $tone
        ? __('Energy rating band :band, score :score', ['band' => $band, 'score' => $score ?? '—'])
        : __('Energy rating not supplied');
@endphp

@if ($tone)
    {{-- C, D and E are light enough that white on them fails contrast, hence
         the literal ink above: the band colour is fixed by the certificate, so
         its text must not follow the theme either. --}}
    <span {{ $attributes->class([
              'inline-flex min-w-8 items-center justify-center font-mono text-micro font-semibold',
              $tone,
          ]) }}
          style="padding:0.1875rem 0.75rem 0.1875rem 0.5rem;
                 clip-path:polygon(0 0,calc(100% - 8px) 0,100% 50%,calc(100% - 8px) 100%,0 100%);"
          role="img"
          aria-label="{{ $label }}">{{ $band }}</span>
@else
    {{-- No band on the record. Say so rather than paint a colour implying one. --}}
    <span {{ $attributes->class('font-mono text-micro text-ink-400') }}>{{ __('Not supplied') }}</span>
@endif
