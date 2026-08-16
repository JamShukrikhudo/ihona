@props(['tone' => 'info'])

@php
    // A chip states a fact with a number — "Reduced £15k", "New — 2 days" —
    // never a mood. Tone carries the meaning alongside the words, never instead
    // of them.
    $tones = [
        'verified' => 'bg-verdigris-100 text-verdigris-700',
        'info' => 'bg-draft-100 text-draft-700',
        'caution' => 'bg-caution-100 text-caution-700',
        'fault' => 'bg-fault-100 text-fault-700',
        'new' => 'bg-survey-100 text-survey-700',
    ];
@endphp

<span {{ $attributes->class([
    'inline-flex items-center gap-1.5 rounded-tag px-2 py-1 font-mono text-annotation font-medium uppercase',
    $tones[$tone] ?? $tones['info'],
]) }}>{{ $slot }}</span>
