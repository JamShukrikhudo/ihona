@props([
    'property',
    // The wishlist control only appears where a Livewire parent can handle it.
    // Rendering it on a static page would give the visitor a dead button.
    'saveable' => false,
    'saved' => false,
    // One primary per view. On a page that already has a primary action — the
    // home page's search — a row of card CTAs would compete with it. The whole
    // card is a link either way.
    'actions' => true,
])

@php
    $currency = $property->currencySymbol();

    $band = $property->energyBand();
    $score = $property->energyScore();
    $perSqFt = $property->pricePerSquareFootForHumans();
    $daysListed = $property->daysListed();
    $photo = $property->getFirstMediaUrl('images') ?: null;
    // Sold, let, under offer. Read from status rather than sold_date, which is
    // nullable and rarely written.
    $closedState = $property->closedStateLabel();
@endphp

<article {{ $attributes->class([
    'property-card @container group relative flex flex-col overflow-hidden rounded-sheet border border-sheet-300',
    'bg-sheet-000 shadow-lift-1 dark:shadow-black/50 transition-[box-shadow,border-color,transform] duration-[280ms] ease-set',
    'hover:-translate-y-0.5 hover:border-ink-400 hover:shadow-lift-3 dark:hover:shadow-black/60',
]) }}>
    <div class="relative aspect-3/2 overflow-hidden bg-sheet-200">
        @if ($photo)
            <img src="{{ $photo }}" alt=""
                 loading="lazy"
                 class="h-full w-full object-cover transition-transform duration-[480ms] ease-set group-hover:scale-[1.04]" />
        @else
            <x-property-elevation :seed="$property->id" />
        @endif

        @if ($closedState)
            <div class="absolute left-2.5 top-2.5">
                <x-ui.chip tone="info">{{ $closedState }}</x-ui.chip>
            </div>
        @elseif ($daysListed === 0)
            <div class="absolute left-2.5 top-2.5">
                <x-ui.chip tone="new">{{ __('New — today') }}</x-ui.chip>
            </div>
        @endif

        @if ($saveable)
            <button type="button"
                    wire:click.stop="toggleFavorite({{ $property->id }})"
                    aria-pressed="{{ $saved ? 'true' : 'false' }}"
                    aria-label="{{ $saved
                        ? __('Remove :title from your wishlist', ['title' => $property->title])
                        : __('Save :title to your wishlist', ['title' => $property->title]) }}"
                    class="absolute right-2 top-2 z-10 grid size-9 place-items-center rounded-sheet bg-sheet-000 shadow-lift-1 transition-colors duration-[160ms] hover:bg-sheet-200">
                <x-ui.icon name="bookmark"
                           class="size-4 {{ $saved ? 'fill-survey-500 text-survey-500' : 'text-ink-900' }}" />
            </button>
        @endif

        @if (! $closedState && $daysListed !== null && $daysListed >= 1 && $daysListed <= 7)
            <div class="absolute left-2.5 top-2.5">
                {{-- A fact with a number, never a mood. --}}
                <x-ui.chip tone="new">
                    {{ trans_choice('New — :count day|New — :count days', $daysListed, ['count' => $daysListed]) }}
                </x-ui.chip>
            </div>
        @endif
    </div>

    <div class="flex flex-1 flex-col p-4">
        <p class="font-display text-h4 font-bold tracking-tight tabular-nums text-ink-900">
            {{ $currency }}{{ number_format((float) $property->price) }}@if ($property->isRental())<span class="ml-1 font-mono text-caption font-normal tracking-normal text-ink-400">{{ __('pcm') }}</span>@endif
        </p>

        <p class="mt-0.5 text-body-s text-ink-700">
            @if ($property->bedrooms){{ $property->bedrooms }} {{ __('bed') }} @endif{{ strtolower($property->property_type ?: __('property')) }}
        </p>

        {{-- The whole card is one link target; the controls below sit above it
             on the stacking order so they stay separately clickable. --}}
        <a href="{{ route('property.detail', $property->id) }}"
           class="absolute inset-0 z-0"
           aria-label="{{ __('View :title', ['title' => $property->title]) }}"></a>

        <p class="text-caption text-ink-400">{{ $property->title }}</p>

        {{-- Only when it is short. Below 80 years the freeholder can charge
             marriage value to extend, so the number changes what the property
             is worth; a 950-year lease is not news and would only add noise. --}}
        @if ($property->hasShortLease())
            <div class="relative z-10 mt-2">
                <x-ui.chip tone="caution">
                    {{ __(':count-year lease', ['count' => $property->lease_years_remaining]) }}
                </x-ui.chip>
            </div>
        @endif

        <div class="mt-3 flex flex-wrap items-center gap-4 text-caption text-ink-500">
            <span class="inline-flex items-center gap-1.5">
                <x-ui.icon name="bedrooms" class="size-4 text-ink-400" />{{ $property->bedrooms ?? '—' }}
            </span>
            <span class="inline-flex items-center gap-1.5">
                <x-ui.icon name="bathrooms" class="size-4 text-ink-400" />{{ $property->bathrooms ?? '—' }}
            </span>
            <span class="inline-flex items-center gap-1.5">
                <x-ui.icon name="floor-area" class="size-4 text-ink-400" />
                @if ($property->area_sqft)
                    {{ number_format((float) $property->area_sqft) }} {{ __('sq ft') }}
                @else
                    {{ __('Not supplied') }}
                @endif
            </span>
        </div>
    </div>

    {{-- THE SIGNATURE: five facts the record already holds, where a listing
         card normally carries a FEATURED flash. Mono and tabular so a column of
         results lines up down the page. Below 380px the last two cells drop and
         energy, price per square foot and days listed survive. --}}
    <dl class="relative z-10 flex border-y border-sheet-300 bg-sheet-100 font-mono tabular-nums">
        <div class="min-w-0 flex-1 px-[7px] py-2">
            <dt class="whitespace-nowrap text-[9.5px] uppercase tracking-[0.06em] text-ink-400">{{ __('EPC') }}</dt>
            <dd class="mt-0.5 flex h-[18px] items-center gap-1 text-[11.5px] font-medium text-ink-900">
                @if ($band)
                    <x-ui.epc-band :band="$band" :score="$score" />{{ $score ?? '' }}
                @else
                    <x-ui.not-supplied />
                @endif
            </dd>
        </div>

        <div class="min-w-0 flex-1 border-l border-sheet-300 px-[7px] py-2">
            <dt class="whitespace-nowrap text-[9.5px] uppercase tracking-[0.06em] text-ink-400">
                {{ $property->pricePerSquareFootLabel() }}
            </dt>
            <dd class="mt-0.5 flex h-[18px] items-center truncate text-[11.5px] font-medium text-ink-900">
                @if ($perSqFt !== null)
                    {{ $perSqFt }}
                @else
                    <x-ui.not-supplied />
                @endif
            </dd>
        </div>

        <div class="min-w-0 flex-1 border-l border-sheet-300 px-[7px] py-2">
            <dt class="whitespace-nowrap text-[9.5px] uppercase tracking-[0.06em] text-ink-400">{{ __('Listed') }}</dt>
            <dd class="mt-0.5 flex h-[18px] items-center truncate text-[11.5px] font-medium text-ink-900">
                @if ($daysListed === 0 && ! $closedState)
                    {{ __('Today') }}
                @elseif ($daysListed !== null)
                    {{ trans_choice(':count day|:count days', $daysListed, ['count' => $daysListed]) }}
                @else
                    <x-ui.not-supplied />
                @endif
            </dd>
        </div>

        <div class="hidden min-w-0 flex-1 border-l border-sheet-300 px-[7px] py-2 @[19rem]:block">
            <dt class="whitespace-nowrap text-[9.5px] uppercase tracking-[0.06em] text-ink-400">{{ __('Built') }}</dt>
            <dd class="mt-0.5 flex h-[18px] items-center truncate text-[11.5px] font-medium text-ink-900">
                @if ($property->year_built)
                    {{ $property->year_built }}
                @else
                    <x-ui.not-supplied />
                @endif
            </dd>
        </div>

        <div class="hidden min-w-0 flex-1 border-l border-sheet-300 px-[7px] py-2 @[19rem]:block">
            <dt class="whitespace-nowrap text-[9.5px] uppercase tracking-[0.06em] text-ink-400">{{ __('Transit') }}</dt>
            <dd class="mt-0.5 flex h-[18px] items-center truncate text-[11.5px] font-medium text-ink-900">
                @if ($property->transit_score !== null)
                    {{ (int) $property->transit_score }}/100
                @else
                    <x-ui.not-supplied />
                @endif
            </dd>
        </div>
    </dl>

    @if ($actions)
        <div class="relative z-10 flex items-center gap-2.5 p-3.5">
            @if ($closedState)
                {{-- No viewing to offer on a property that is no longer for
                     sale, and a live booking button on one reads as an
                     available home. --}}
                <x-ui.button size="sm" variant="secondary" :href="route('property.detail', $property->id)">
                    {{ __('View details') }}
                </x-ui.button>
            @else
            <x-ui.button size="sm" :href="route('property.book', $property->id)">
                {{ __('Book a viewing') }}
            </x-ui.button>
            <x-ui.button size="sm" variant="ghost" :href="route('contact.show', ['property' => $property->id])">
                {{ __('Ask a question') }}
            </x-ui.button>
            @endif
        </div>
    @endif
</article>
