@props([
    'label' => null,
    'dated' => null,
])

{{--
    What produced this, said on the face of it.

    The page used to mark one block with "AI-Powered Insights" in 12px grey at
    2.6:1 — under AA, and worded as a feature boast rather than a caveat. A
    reader deciding whether to trust a figure has to be able to read the
    sentence that tells them not to, so this is 13px at 6.3:1 with a rule above
    it, and it says which of the two things happened: a model estimated the
    number, or a model wrote the words.
--}}
<p {{ $attributes->class('flex flex-wrap items-center gap-x-2 gap-y-1 font-mono text-caption text-ink-500') }}>
    <x-ui.icon name="alert" class="size-3.5 shrink-0 text-caution-600" />
    <span>{{ $label }}</span>
    @if ($dated)
        <span class="text-ink-400">{{ __('on :date', ['date' => $dated->format('j M Y')]) }}</span>
    @endif
    @if (! $slot->isEmpty())
        <span class="text-ink-400">{{ $slot }}</span>
    @endif
</p>
