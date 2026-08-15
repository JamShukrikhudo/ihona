@php
    $applied = $this->appliedFilters();
    $labels = $this->filterLabels();

    // Resolved before anything renders, so a query failure has already flashed
    // by the time the banner below is reached. The banner used to sit above
    // this point, so an outage rendered as "no homes match these filters" and
    // the real message surfaced on someone's next, healthy visit.
    $results = $this->properties;

    // total() rather than a second COUNT with the same filters: paginate()
    // has already counted them.
    $count = $results->total();
@endphp

<div class="mx-auto max-w-(--breakpoint-xl) px-4 py-band md:px-margin">

    <header>
        <p class="font-mono text-annotation uppercase text-ink-400">{{ __('Sales and lettings') }}</p>
        <h1 class="mt-3 font-display text-h2 font-bold tracking-tight text-ink-900">
            {{ __('Every home on our books') }}
        </h1>
    </header>

    {{-- The search and the applied set sit together, because what you typed and
         what is narrowing the page are the same question. --}}
    <div class="mt-6 flex flex-wrap items-center gap-2 rounded-sheet border border-sheet-300 bg-sheet-000 p-2 shadow-lift-1">
        <label for="listing-search" class="sr-only">{{ __('Search') }}</label>
        <div class="flex flex-1 basis-64 items-center gap-2 px-2">
            <x-ui.icon name="search" class="size-4 shrink-0 text-ink-400" />
            <input id="listing-search" type="search" wire:model.live.debounce.300ms="search"
                   placeholder="{{ __('Postcode, station or area — e.g. RG1') }}"
                   class="w-full border-0 bg-transparent p-0 py-2.5 font-sans text-body-s text-ink-900 placeholder:text-sheet-400 focus:ring-0 focus:outline-none" />
        </div>

        <label for="listing-type" class="sr-only">{{ __('Property type') }}</label>
        <select id="listing-type" wire:model.live="propertyType"
                class="basis-40 rounded-sheet border border-sheet-300 bg-sheet-000 px-3 py-2.5 font-sans text-body-s text-ink-900">
            <option value="">{{ __('Any type') }}</option>
            @foreach (\App\Models\Property::TYPES as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>

        <label for="listing-beds" class="sr-only">{{ __('Minimum bedrooms') }}</label>
        <select id="listing-beds" wire:model.live="minBedrooms"
                class="basis-36 rounded-sheet border border-sheet-300 bg-sheet-000 px-3 py-2.5 font-sans text-body-s text-ink-900">
            <option value="">{{ __('Any beds') }}</option>
            @foreach ([1, 2, 3, 4, 5] as $beds)
                <option value="{{ $beds }}">{{ $beds }}+</option>
            @endforeach
        </select>
    </div>

    {{-- What is narrowing the page, stated. A filter the visitor cannot see is
         one they cannot argue with, and quiet defaults have hidden stock here
         before. --}}
    <div class="mt-4 flex flex-wrap items-center gap-3">
        <p class="font-mono text-body-s font-medium tabular-nums text-ink-900" aria-live="polite">
            {{ trans_choice(':count home|:count homes', $count, ['count' => number_format($count)]) }}
        </p>

        @if ($applied)
            <span class="h-4 w-px bg-sheet-300" aria-hidden="true"></span>

            <ul class="flex flex-wrap items-center gap-2">
                @foreach ($applied as $filter => $description)
                    <li>
                        <button type="button" wire:click="clearFilter('{{ $filter }}')"
                                class="group inline-flex items-center gap-1.5 rounded-tag border border-sheet-300 bg-sheet-000 py-1.5 pl-2.5 pr-2 font-mono text-annotation uppercase text-ink-700 transition-colors duration-[160ms] hover:border-ink-900"
                                aria-label="{{ __('Clear :filter', ['filter' => $labels[$filter] ?? $filter]) }}">
                            {{ $description }}
                            <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="square" aria-hidden="true"
                                 class="text-ink-400 group-hover:text-ink-900">
                                <path d="M6 6l12 12M18 6L6 18" />
                            </svg>
                        </button>
                    </li>
                @endforeach
            </ul>

            <button type="button" wire:click="clearFilters"
                    class="font-mono text-annotation uppercase text-survey-600 transition-colors duration-[160ms] hover:text-ink-900">
                {{ __('Clear all') }}
            </button>
        @endif
    </div>

    @if (session('error'))
        <p role="alert" class="mt-4 rounded-sheet border border-fault-600 bg-fault-100 px-4 py-3 text-body-s text-fault-700">
            {{ session('error') }}
        </p>
    @endif

    <div class="mt-6 grid gap-6 lg:grid-cols-12">
        <div class="lg:col-span-7 xl:col-span-8">
            @if (count($results))
                <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($results as $property)
                        <x-property-card :property="$property"
                                         saveable
                                         :saved="$this->isFavorited($property->id)" />
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $results->links() }}
                </div>
            @elseif ($count)
                {{-- Matches exist, just not on this page: an out-of-range page
                     number, not an empty search. --}}
                <div class="rounded-sheet border border-dashed border-sheet-300 bg-sheet-000 p-10 text-center">
                    <p class="font-display text-h4 font-bold tracking-tight text-ink-900">
                        {{ __('Nothing on this page') }}
                    </p>
                    <p class="mx-auto mt-2 max-w-reading text-body-s text-ink-500">
                        {{ trans_choice(
                            ':count home matches, starting from the first page.|:count homes match, starting from the first page.',
                            $count,
                            ['count' => number_format($count)]
                        ) }}
                    </p>
                    <div class="mt-4">
                        <x-ui.button size="sm" type="button" wire:click="gotoPage(1)">
                            {{ __('Back to the first page') }}
                        </x-ui.button>
                    </div>
                </div>
            @else
                @php $loosen = rescue(fn () => $this->mostRestrictiveFilter(), null, report: false); @endphp

                {{-- An invitation, not a dead end: the move and what it returns. --}}
                <div class="rounded-sheet border border-dashed border-sheet-300 bg-sheet-000 p-10 text-center">
                    <p class="font-display text-h4 font-bold tracking-tight text-ink-900">
                        {{ __('No homes match these filters') }}
                    </p>

                    @if ($loosen)
                        <p class="mx-auto mt-2 max-w-reading text-body-s text-ink-500">
                            {{ __('Clear :filter and :count come back.', [
                                'filter' => $labels[$loosen['filter']] ?? $loosen['filter'],
                                'count' => trans_choice(':count home|:count homes', $loosen['count'], ['count' => number_format($loosen['count'])]),
                            ]) }}
                        </p>
                        <div class="mt-4">
                            <x-ui.button size="sm" type="button" wire:click="clearFilter('{{ $loosen['filter'] }}')">
                                {{ __('Clear :filter', ['filter' => $labels[$loosen['filter']] ?? $loosen['filter']]) }}
                            </x-ui.button>
                        </div>
                    @else
                        <p class="mx-auto mt-2 max-w-reading text-body-s text-ink-500">
                            {{ __('Nothing is listed right now. New homes appear here as they come to market.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- One map, not two. A second instance would build its own Leaflet
             and fetch its own tiles for whichever breakpoint was not showing.
             Above 1024px it is open and sticky beside the results; below, it is
             a control the reader opens rather than a half-height strip nobody
             can pan. --}}
        <aside class="lg:col-span-5 xl:col-span-4">
            <details data-map-panel class="group lg:sticky lg:top-6" wire:ignore>
                <summary class="flex cursor-pointer items-center justify-between rounded-sheet border border-sheet-300 bg-sheet-000 px-4 py-3 font-mono text-annotation uppercase text-ink-700 lg:hidden">
                    {{ __('Show these homes on a map') }}
                    <x-ui.icon name="chevron-right" class="size-3.5 transition-transform group-open:rotate-90" />
                </summary>
                <div class="mt-3 lg:mt-0">
                    <x-property-map :properties="$mapPoints" />
                </div>
            </details>
        </aside>

        @once
            @push('scripts')
                <script>
                    // Open above 1024px, where the map holds its place beside the
                    // results. A closed <details> cannot be forced open by CSS,
                    // and duplicating the map to work around that would mean two
                    // Leaflet instances and two sets of tiles.
                    (function () {
                        var panel = document.querySelector('[data-map-panel]');

                        if (! panel) return;

                        var wide = window.matchMedia('(min-width: 1024px)');
                        var sync = function () {
                            if (wide.matches) panel.setAttribute('open', '');
                        };

                        sync();
                        wide.addEventListener('change', sync);
                    })();
                </script>
            @endpush
        @endonce

    </div>
</div>
