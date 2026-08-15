@extends('layouts.app')

{{-- No @section('styles') here: Leaflet's stylesheet is already bundled by
     resources/js/app.js. The unpkg <link> that used to sit here was dead until
     the layout gained @yield('styles'), at which point it became a
     render-blocking third-party request for CSS the page already had. --}}

@section('content')
    <div class="mx-auto max-w-(--breakpoint-xl) px-4 md:px-margin">

        {{-- The hero leads with the search, because that is the one thing a
             visitor came here to do. No lifestyle photograph with text over the
             middle of it: it says nothing anyone can act on. --}}
        <section class="py-band">
            <p class="font-mono text-annotation uppercase text-ink-400">
                {{ __('Sales · Lettings · Property management') }}
            </p>

            <h1 class="mt-3 max-w-[18ch] font-display text-h1 font-bold text-ink-900">
                {{ __('Find the house, then the facts.') }}
            </h1>

            <p class="mt-4 max-w-reading text-body-l text-ink-500">
                {{ __('Every listing carries its energy rating, price per square foot and how long it has been on the market — on the card, before you click.') }}
            </p>

            <form action="{{ route('property.list') }}" method="GET"
                  class="mt-8 flex flex-wrap gap-2 rounded-sheet border border-sheet-300 bg-sheet-000 p-2 shadow-lift-2">
                <label for="home-search" class="sr-only">{{ __('Where are you looking?') }}</label>
                <div class="flex flex-1 basis-64 items-center gap-2 px-2">
                    <x-ui.icon name="search" class="size-4 shrink-0 text-ink-400" />
                    <input id="home-search" type="search" name="search"
                           placeholder="{{ __('Postcode, station or area — e.g. RG1') }}"
                           class="w-full border-0 bg-transparent p-0 py-2.5 font-sans text-body-s text-ink-900 placeholder:text-sheet-400 focus:ring-0 focus:outline-none" />
                </div>

                <label for="home-type" class="sr-only">{{ __('Property type') }}</label>
                <select id="home-type" name="propertyType"
                        class="basis-40 rounded-sheet border border-sheet-300 bg-sheet-000 px-3 py-2.5 font-sans text-body-s text-ink-900">
                    <option value="">{{ __('Any type') }}</option>
                    <option value="house">{{ __('House') }}</option>
                    <option value="apartment">{{ __('Apartment') }}</option>
                    <option value="condo">{{ __('Condo') }}</option>
                    <option value="townhouse">{{ __('Townhouse') }}</option>
                </select>

                <label for="home-beds" class="sr-only">{{ __('Minimum bedrooms') }}</label>
                <select id="home-beds" name="minBedrooms"
                        class="basis-36 rounded-sheet border border-sheet-300 bg-sheet-000 px-3 py-2.5 font-sans text-body-s text-ink-900">
                    <option value="">{{ __('Any beds') }}</option>
                    @foreach ([1, 2, 3, 4, 5] as $beds)
                        <option value="{{ $beds }}">{{ $beds }}+</option>
                    @endforeach
                </select>

                <x-ui.button type="submit" class="basis-full sm:basis-auto">
                    {{ __('Search homes') }}
                </x-ui.button>
            </form>
        </section>

        {{-- Featured properties reuse the card, so a change to the disclosure
             strip lands here too. No CTA row: the search above is the page's
             one primary action, and the whole card is already a link. --}}
        <section class="pb-band">
            <div class="mb-5 flex items-center gap-3">
                <h2 class="font-display text-h3 font-bold tracking-tight text-ink-900">
                    {{ __('Featured homes') }}
                </h2>
                <span class="h-px flex-1 bg-sheet-300" aria-hidden="true"></span>
                <a href="{{ route('property.list') }}"
                   class="inline-flex items-center gap-1.5 font-mono text-annotation uppercase text-ink-500 transition-colors duration-[160ms] hover:text-ink-900">
                    {{ __('Browse all homes') }}
                    <x-ui.icon name="chevron-right" class="size-3.5" />
                </a>
            </div>

            @if (count($featuredProperties))
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($featuredProperties as $property)
                        <x-property-card :property="$property" :actions="false" />
                    @endforeach
                </div>
            @else
                <div class="rounded-sheet border border-dashed border-sheet-300 bg-sheet-000 p-10 text-center">
                    <p class="font-display text-h4 font-bold tracking-tight text-ink-900">
                        {{ __('Nothing is featured right now') }}
                    </p>
                    <p class="mx-auto mt-2 max-w-reading text-body-s text-ink-500">
                        {{ __('The full list is still there, and the search above will narrow it.') }}
                    </p>
                    <a href="{{ route('property.list') }}"
                       class="mt-4 inline-flex items-center gap-1.5 font-mono text-annotation uppercase text-survey-600 hover:text-ink-900">
                        {{ __('Browse all homes') }}
                        <x-ui.icon name="chevron-right" class="size-3.5" />
                    </a>
                </div>
            @endif
        </section>

        {{-- The map is the heaviest thing on the page, so it sits below the
             featured homes and draws nothing until the reader scrolls to it. --}}
        <section class="pb-band">
            <div class="mb-5 flex items-center gap-3">
                <h2 class="font-display text-h3 font-bold tracking-tight text-ink-900">
                    {{ __('Where they are') }}
                </h2>
                <span class="h-px flex-1 bg-sheet-300" aria-hidden="true"></span>
            </div>

            <x-property-map />
        </section>
    </div>
@endsection
