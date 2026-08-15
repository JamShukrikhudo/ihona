@props([
    'as' => 'input',
    'invalid' => false,
])

{{--
    One treatment for every control on the site.

    The class string used to live inline in contact-form.blade.php, which meant
    the next form either imported that file's opinion by hand or drifted from
    it — and the auth pages, six forms, had drifted so far they were still on
    the Jetstream defaults. `as` picks the element so an input, a select and a
    textarea are the same control with different content.

    The focus ring is not set here: app.css gives every focusable element the
    same outline, and a ring utility on top of it would draw two.
--}}
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
