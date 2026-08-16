@php
    $closedState = $property->closedStateLabel();
    // The stars used to be drawn once per review, so a property with four
    // reviews showed twenty of them and four "N Reviews" links in a row.
    $rating = $reviews->count() ? round($reviews->avg('rating'), 1) : null;
@endphp

<h1 class="font-display text-h3 font-bold tracking-tight text-ink-900">
    {{ $property->title }}
</h1>

<div class="mt-3 flex flex-wrap items-baseline gap-x-4 gap-y-2">
    <p class="font-display text-h2 font-bold tabular-nums tracking-tight text-ink-900">
        {{ $property->currencySymbol() }}{{ number_format((float) $property->price) }}@if ($property->isRental())<span class="ml-1 font-mono text-body-s font-normal tracking-normal text-ink-400">{{ __('pcm') }}</span>@endif
    </p>

    @if ($closedState)
        <x-ui.chip tone="info">{{ $closedState }}</x-ui.chip>
    @endif

    @if ($rating)
        <p class="font-mono text-caption tabular-nums text-ink-500">
            {{ __(':rating out of 5', ['rating' => $rating]) }}
            <span class="text-ink-400">
                ({{ trans_choice(':count review|:count reviews', $reviews->count(), ['count' => $reviews->count()]) }})
            </span>
        </p>
    @endif
</div>

<div class="mt-6 flex flex-wrap items-center gap-2.5">
    @if ($closedState)
        {{-- No viewing to offer on a property that is no longer for sale. --}}
        <x-ui.button variant="secondary" :href="route('contact.show', ['property' => $property->id])">
            {{ __('Ask a question') }}
        </x-ui.button>
    @else
        {{-- Was data-modal-target="scheduleViewingModal": that dialog's body was
             gated @if(false), so it opened empty, and its form posted to a
             bookViewing() this component does not define. --}}
        <x-ui.button :href="route('property.book', $property->id)">
            {{ __('Book a viewing') }}
        </x-ui.button>
        <x-ui.button variant="secondary" :href="route('contact.show', ['property' => $property->id])">
            {{ __('Ask a question') }}
        </x-ui.button>
    @endif

    <x-ui.button variant="ghost"
                 wire:click="toggleFavorite"
                 aria-pressed="{{ $isFavorited ? 'true' : 'false' }}">
        <x-ui.icon name="bookmark" class="size-4 {{ $isFavorited ? 'fill-survey-500 text-survey-500' : '' }}" />
        {{ $isFavorited ? __('Saved') : __('Save') }}
    </x-ui.button>
</div>

{{-- The "Book valuation" control that used to sit here opened a dialog whose
     body was gated @if(false) — an empty box with a Decline button and a
     submit that posted nowhere. The valuation the site can actually produce is
     linked below, and it says what produced it. --}}
<p class="mt-3">
    <a href="{{ route('property.valuation', ['propertyId' => $property->id]) }}"
       class="inline-flex items-center gap-1.5 font-mono text-caption text-draft-500 underline underline-offset-2 hover:no-underline">
        {{ __('See a model-estimated valuation for this property') }}
        <x-ui.icon name="chevron-right" class="size-3.5" />
    </a>
</p>

{{-- What the record holds, each fact with a dated source. This is the block a
     buyer decides on, so it sits above the tours and the gallery. --}}
<x-property-disclosure :property="$property" class="mt-6" />
