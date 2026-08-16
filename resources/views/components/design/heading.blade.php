@props([
    'index' => null,
    'title' => null,
])

{{--
    The dimension line: a measured rule with end ticks, the way a drawing marks
    a span. The index is a real sequence here — the styleguide is read in order.
--}}
<div class="mb-5 flex items-center gap-3">
    @if ($index)
        <span class="font-mono text-annotation tracking-[0.09em] text-survey-600">{{ $index }}</span>
    @endif
    <h2 class="font-display text-h3 font-bold tracking-tight text-ink-900">{{ $title }}</h2>
    <span class="h-px flex-1 bg-sheet-300"
          style="box-shadow:0 -3px 0 -2px var(--color-sheet-300), 0 3px 0 -2px var(--color-sheet-300);"
          aria-hidden="true"></span>
</div>
