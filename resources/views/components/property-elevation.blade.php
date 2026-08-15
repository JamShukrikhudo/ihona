@props(['seed' => 0])

{{--
    The missing-photo treatment: an elevation drawn in ink on the sheet, rather
    than a grey camera glyph. A listing without a photograph should still look
    considered, and the fixed aspect ratio keeps the grid from jumping when one
    arrives.

    Three elevations, chosen by the property id so the same listing always draws
    the same house.
--}}
@php
    $elevations = [
        // Victorian semi
        'M60 300 H540 M170 300 V150 M430 300 V150 M150 152 L300 76 L450 152 Z'
        .' M370 118 V60 H398 V133 M262 300 V230 H338 V300'
        .' M196 272 V212 H252 V272 M196 242 H252 M224 212 V272'
        .' M348 272 V212 H404 V272 M348 242 H404 M376 212 V272'
        .' M196 196 V158 H244 V196 M220 158 V196'
        .' M276 196 V158 H324 V196 M300 158 V196'
        .' M356 196 V158 H404 V196 M380 158 V196',
        // Apartment block
        'M60 300 H540 M160 300 V90 H440 V300 M160 82 H440'
        .' M186 116 H228 V146 H186 Z M256 116 H298 V146 H256 Z M326 116 H368 V146 H326 Z M396 116 H438 V146 H396 Z'
        .' M186 166 H228 V196 H186 Z M256 166 H298 V196 H256 Z M326 166 H368 V196 H326 Z M396 166 H438 V196 H396 Z'
        .' M186 216 H228 V246 H186 Z M396 216 H438 V246 H396 Z'
        .' M268 300 V216 H332 V300',
        // Detached cottage
        'M60 300 H540 M180 300 V180 M420 300 V180 M162 182 L300 110 L438 182 Z'
        .' M352 128 V78 H378 V146 M272 300 V222 H328 V300'
        .' M206 268 V214 H262 V268 M206 241 H262 M234 214 V268'
        .' M338 268 V214 H394 V268 M338 241 H394 M366 214 V268'
        .' M90 300 V276 H146 V300',
    ];

    $paths = $elevations[abs((int) $seed) % count($elevations)];
@endphp

<svg {{ $attributes->class('h-full w-full bg-sheet-200') }}
     viewBox="0 0 600 400"
     fill="none"
     preserveAspectRatio="xMidYMid meet"
     role="img"
     aria-label="{{ __('No photograph supplied') }}">
    <g stroke="var(--color-sheet-300)" stroke-width="1">
        @for ($x = 0; $x < 600; $x += 25)
            <path d="M{{ $x }} 0 V400" />
        @endfor
        @for ($y = 0; $y < 400; $y += 25)
            <path d="M0 {{ $y }} H600" />
        @endfor
    </g>
    <path d="{{ $paths }}"
          stroke="var(--color-ink-400)"
          stroke-width="2"
          stroke-linecap="square"
          stroke-linejoin="miter" />
</svg>
