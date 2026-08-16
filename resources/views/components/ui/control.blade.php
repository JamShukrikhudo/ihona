@props([
    'as' => 'input',
    'invalid' => false,
])

{{-- One treatment for every control. `as` picks the element. No focus ring
     here — app.css already gives every focusable element one. --}}
@php
    $classes = 'w-full rounded-sheet border border-sheet-300 bg-sheet-000 px-3.5 py-[11px]'
        .' font-sans text-body-s text-ink-900 placeholder:text-sheet-400'
        .' transition-[border-color,box-shadow] duration-[160ms]'
        .' hover:border-ink-400 focus:border-survey-500 focus:ring-0 focus:outline-none';
@endphp

@if ($as === 'input')
    <input {{ $attributes->class([$classes, 'border-fault-600' => $invalid]) }}>
@elseif ($as === 'select')
    <select {{ $attributes->class([$classes, 'border-fault-600' => $invalid]) }}>{{ $slot }}</select>
@else
    <textarea {{ $attributes->class([$classes, 'border-fault-600' => $invalid]) }}>{{ $slot }}</textarea>
@endif
