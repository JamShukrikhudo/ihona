@props([
    'variant' => 'primary',
    'size' => 'base',
    'href' => null,
    'disabled' => false,
])

@php
    // One primary per view. A control says exactly what happens when it is used
    // — "Book a viewing", never "Submit" — and its confirmation reuses the verb.
    // No border-color in the base: it and the variant's would be the same
    // utility, so the generated source order would decide which wins rather
    // than the order they are written here. Every variant sets its own.
    // 44px minimum where a finger is doing the pointing. At the default size a
    // button measured 38px, under the floor the system sets for touch, while
    // the fields beside it were 46px.
    $base = 'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-sheet border pointer-coarse:min-h-11'
        .' font-sans font-semibold leading-none transition-[background-color,border-color,box-shadow,transform]'
        .' duration-[160ms] ease-snap active:translate-y-px'
        .' disabled:pointer-events-none disabled:opacity-45 disabled:active:translate-y-0'
        .' aria-disabled:pointer-events-none aria-disabled:opacity-45';

    $variants = [
        'primary' => 'border-action bg-action text-white shadow-lift-1 hover:border-action-hover hover:bg-action-hover hover:shadow-lift-2',
        'secondary' => 'border-sheet-300 bg-sheet-000 text-ink-900 hover:border-ink-900',
        'ghost' => 'border-transparent bg-transparent text-ink-500 hover:bg-sheet-200 hover:text-ink-900',
        'danger' => 'border-fault-600 bg-fault-600 text-white hover:border-fault-700 hover:bg-fault-700',
    ];

    $sizes = [
        'sm' => 'px-[13px] py-2 text-caption',
        'base' => 'px-5 py-[11px] text-body-s',
        'lg' => 'px-[26px] py-[15px] text-body',
    ];

    $classes = trim($base.' '.($variants[$variant] ?? $variants['primary']).' '.($sizes[$size] ?? $sizes['base']));
@endphp

@if ($href && ! $disabled)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@elseif ($href)
    {{-- A disabled link is not a thing; render the inert control instead. --}}
    <span role="link" aria-disabled="true" {{ $attributes->class($classes) }}>{{ $slot }}</span>
@else
    <button {{ $attributes->merge(['type' => 'button'])->class($classes) }} @disabled($disabled)>
        {{ $slot }}
    </button>
@endif
