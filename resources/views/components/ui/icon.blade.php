@props([
    'name' => null,
    'label' => null,
])

@php
    // Drawn on a 24px grid with a 1.5px stroke, square caps and mitred joins —
    // the geometry of a technical pen, not a rounded lifestyle set.
    $paths = [
        'bedrooms' => ['M3 18v-7h18v7', 'M3 18v2M21 18v2', 'M6 11V7h5v4', 'M13 11V7h5v4'],
        'bathrooms' => ['M3 12h18v3a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4z', 'M6 12V5h4v3', 'M6 19v2M18 19v2'],
        'floor-area' => ['M3 3h18v18H3z', 'M3 9h6V3M15 21v-6h6'],
        'floor-plan' => ['M3 4h18v16H3z', 'M3 12h8V4M11 20v-8h10', 'M15 4v4'],
        'aspect' => ['M12 4v16M4 12h16', 'M12 2 15 8H9z'],
        'certificate' => ['M3 3h13l5 5v13H3z', 'M16 3v5h5', 'M7 13l3 3 5-6'],
        'epc' => ['M3 4h12l4 3.5-4 3.5H3z', 'M3 13h8l4 3.5-4 3.5H3z'],
        'transport' => ['M6 3h12v13H6z', 'M6 9h12', 'M9 12h2M13 12h2', 'M8 16l-3 5M16 16l3 5'],
        'location' => ['M12 21s7-6.4 7-11a7 7 0 1 0-14 0c0 4.6 7 11 7 11z', 'M10.5 8.5h3v3h-3z'],
        'chain' => ['M3 3h7v6H3z', 'M14 15h7v6h-7z', 'M6.5 9v4h11v2'],
        'viewing' => ['M4 5h16v16H4z', 'M4 10h16M9 3v4M15 3v4', 'M8 14h3v3H8z'],
        'price' => ['M3 3h8l10 10-8 8L3 11z', 'M6 6h2.5v2.5H6z'],
        'tenure' => ['M9 3h6v4H9z', 'M5 7h14v14H5z', 'M9 12h6M9 16h4'],
        'tour' => ['M12 3 3 8v8l9 5 9-5V8z', 'M3 8l9 5 9-5M12 13v10'],
        'property' => ['M12 3 3 9v12h18V9z', 'M9 21v-7h6v7'],
        'enquiry' => ['M4 4h16v13H7l-3 3z', 'M8 9h8M8 13h5'],
        'alert' => ['M12 3 2 20h20z', 'M12 10v4M12 17h.01'],
        'search' => ['M4 11a7 7 0 1 0 14 0 7 7 0 1 0-14 0', 'M16 16l4 4'],
        'chevron-right' => ['M5 12h14M13 6l6 6-6 6'],
    ];

    if (! isset($paths[$name])) {
        // Fail loudly: a silently missing icon is a hole in the interface that
        // nobody notices until a user reports a blank button.
        throw new \InvalidArgumentException(
            "Unknown icon [{$name}]. Available: ".implode(', ', array_keys($paths))
        );
    }
@endphp

<svg {{ $attributes->class('size-4') }}
     viewBox="0 0 24 24"
     fill="none"
     stroke="currentColor"
     stroke-width="1.5"
     stroke-linecap="square"
     stroke-linejoin="miter"
     @if ($label) role="img" aria-label="{{ $label }}" @else aria-hidden="true" @endif>
    @foreach ($paths[$name] as $d)
        <path d="{{ $d }}" />
    @endforeach
</svg>
