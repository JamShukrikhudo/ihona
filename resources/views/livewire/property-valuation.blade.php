{{--
    What a home is worth, according to a model.

    The page used to open "Neural Network Property Valuation" and print one
    figure to two decimal places in 36px blue — the most confident presentation
    on the whole site, for the least certain number on it. The service had
    computed a band on every call since it was written and nothing ever read it.

    So: the band is the figure, the midpoint sits under it in a smaller size,
    what the estimate was derived from is listed with the date of that evidence,
    and the way to get a number a person will stand behind is on the page.
--}}
<div class="mx-auto max-w-(--breakpoint-lg) px-4 py-8 md:px-margin md:py-12">
    @php
        $currency = $property?->currencySymbol() ?? '';
    @endphp

    <header class="max-w-reading">
        <h1 class="font-display text-h3 font-bold tracking-tight text-ink-900">
            {{ __('What this home is worth') }}
        </h1>
        <p class="mt-2 text-body text-ink-700">
            {{ __('An estimate from a model, with the range it is honest to quote. A valuer walking the rooms is what turns it into a figure you can sell on.') }}
        </p>
    </header>

    @if ($errorMessage)
        <p class="mt-6 rounded-sheet border border-fault-600 bg-fault-100 px-4 py-3 text-body-s text-fault-700" role="alert">
            {{ $errorMessage }}
        </p>
    @endif

    @if ($property)
        <section class="mt-8 rounded-sheet border border-sheet-300 bg-sheet-000 p-5 sm:p-6" aria-labelledby="subject-heading">
            <h2 id="subject-heading" class="font-display text-h5 font-bold tracking-tight text-ink-900">
                {{ $property->title }}
            </h2>
            <p class="mt-1 text-body-s text-ink-500">{{ $property->location }}</p>

            <dl class="mt-4 grid grid-cols-2 gap-x-4 gap-y-3 sm:grid-cols-4">
                @foreach ([
                    __('Asking price') => $property->price ? $currency.number_format($property->price) : null,
                    __('Type') => $property->property_type ? ucfirst(str_replace('_', ' ', $property->property_type)) : null,
                    __('Bedrooms') => $property->bedrooms,
                    __('Floor area') => $property->area_sqft ? number_format($property->area_sqft).' '.__('sq ft') : null,
                ] as $label => $value)
                    <div class="min-w-0">
                        <dt class="font-mono text-annotation uppercase text-ink-500">{{ $label }}</dt>
                        <dd class="mt-1 truncate font-mono text-body-s font-medium tabular-nums text-ink-900">
                            @if (filled($value))
                                {{ $value }}
                            @else
                                <x-ui.not-supplied />
                            @endif
                        </dd>
                    </div>
                @endforeach
            </dl>
        </section>

        @if ($showReport && $valuation)
            @php
                $range = $valuation->range();
                $trend = $valuation->location_factors['market_trend'] ?? null;
                $comparables = $valuation->comparable_properties['count'] ?? null;
                $epc = $property->epc['rating'] ?? null;
                $evidenceDate = $valuation->valuation_date;
            @endphp

            <section class="mt-6 rounded-sheet border border-sheet-300 bg-sheet-100 p-5 sm:p-6" aria-labelledby="estimate-heading">
                {{-- No "Close": the estimate is what the page is for. It was a
                     dismiss control on the only content the page has. --}}
                <h2 id="estimate-heading" class="font-display text-h5 font-bold tracking-tight text-ink-900">
                    {{ __('Estimated value') }}
                </h2>

                <x-ui.model-note class="mt-1.5"
                                 :label="__('Estimated by a model, not surveyed')"
                                 :dated="$evidenceDate" />

                <div class="mt-4 rounded-sheet border border-sheet-300 bg-sheet-000 p-5">
                    @if ($range)
                        {{-- The range is the figure. The midpoint sits under it,
                             smaller, so it cannot be read as the answer on its
                             own — which is exactly how this page read before. --}}
                        <p class="font-display text-h3 font-bold tabular-nums tracking-tight text-ink-900">
                            {{ $currency }}{{ number_format($range['low']) }}&ndash;{{ $currency }}{{ number_format($range['high']) }}
                        </p>
                        <p class="mt-1.5 font-mono text-micro tabular-nums text-ink-400">
                            {{ __('midpoint :value', ['value' => $currency.number_format($valuation->estimated_value)]) }}
                        </p>
                    @else
                        <p class="text-body-s text-ink-500">{{ __('This valuation carries no figure.') }}</p>
                    @endif

                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <x-ui.chip :tone="$valuation->confidence_level >= 70 ? 'verified' : 'caution'">
                            {{ __('Confidence :level%', ['level' => $valuation->confidence_level ?? 0]) }}
                        </x-ui.chip>
                        <span class="font-mono text-caption text-ink-500">
                            {{ __('The less sure the model is, the wider the range it quotes.') }}
                        </span>
                    </div>
                </div>

                {{-- What it was derived from, and how old that evidence is. A
                     number with no provenance is a number nobody can argue
                     with, which is the opposite of what this page owes. --}}
                <h3 class="mt-6 font-mono text-annotation uppercase text-ink-500">{{ __('Derived from') }}</h3>
                <dl class="mt-2 divide-y divide-sheet-300 rounded-sheet border border-sheet-300 bg-sheet-000">
                    @foreach ([
                        __('Comparable sales') => $comparables ? trans_choice(':count nearby sale|:count nearby sales', $comparables, ['count' => $comparables]) : null,
                        __('Floor area') => $property->area_sqft ? number_format($property->area_sqft).' '.__('sq ft') : null,
                        __('Energy record') => $epc ? __('Band :band', ['band' => $epc]) : null,
                        __('Local market') => $trend ? ucfirst(str_replace('_', ' ', $trend)) : null,
                    ] as $label => $value)
                        <div class="flex flex-wrap items-baseline justify-between gap-2 px-4 py-3">
                            <dt class="text-body-s text-ink-700">{{ $label }}</dt>
                            <dd class="font-mono text-caption tabular-nums text-ink-900">
                                @if (filled($value))
                                    {{ $value }}
                                @else
                                    <x-ui.not-supplied />
                                @endif
                            </dd>
                        </div>
                    @endforeach
                    <div class="flex flex-wrap items-baseline justify-between gap-2 px-4 py-3">
                        <dt class="text-body-s text-ink-700">{{ __('Evidence as at') }}</dt>
                        <dd class="font-mono text-caption tabular-nums text-ink-900">
                            {{ $evidenceDate?->format('j M Y') ?? __('unknown') }}
                        </dd>
                    </div>
                </dl>

                @if (! empty($valuation->location_factors['prediction_factors']))
                    <h3 class="mt-6 font-mono text-annotation uppercase text-ink-500">{{ __('What moved the number') }}</h3>
                    <ul class="mt-2 space-y-1.5">
                        @foreach ($valuation->location_factors['prediction_factors'] as $factor)
                            <li class="flex items-start gap-2 text-body-s text-ink-700">
                                <x-ui.icon name="aspect" class="mt-1 size-3.5 shrink-0 text-ink-400" />
                                <span>{{ $factor }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        @endif

        {{-- The action. A model is free and instant and wrong by a band this
             wide; a valuer is neither free nor instant and worth the call. --}}
        <section class="mt-6 rounded-sheet border border-sheet-300 bg-sheet-000 p-5 sm:p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="max-w-reading">
                    <h2 class="font-display text-h5 font-bold tracking-tight text-ink-900">
                        {{ __('Book a valuation with a valuer') }}
                    </h2>
                    <p class="mt-1 text-body-s text-ink-500">
                        {{ __('Someone walks the rooms, reads the road, and gives you a figure they will stand behind. No cost, no obligation.') }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <x-ui.button :href="route('contact.show', ['property' => $property->id, 'interest' => 'selling'])">
                        {{ __('Book a valuation') }}
                    </x-ui.button>

                    @auth
                        <x-ui.button variant="secondary" wire:click="generateValuation" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="generateValuation">{{ __('Run the estimate again') }}</span>
                            <span wire:loading wire:target="generateValuation">{{ __('Estimating…') }}</span>
                        </x-ui.button>
                    @endauth
                </div>
            </div>

            @guest
                <p class="mt-3 text-body-s text-ink-500">
                    {!! __('Agents can re-run the estimate after :login.', [
                        'login' => '<a class="text-draft-700 underline underline-offset-2 hover:no-underline" href="'.e(url('/login')).'">'.e(__('signing in')).'</a>',
                    ]) !!}
                </p>
            @endguest
        </section>

        @if (count($valuationHistory) > 0)
            <section class="mt-6 rounded-sheet border border-sheet-300 bg-sheet-000 p-5 sm:p-6" aria-labelledby="history-heading">
                <h2 id="history-heading" class="font-display text-h5 font-bold tracking-tight text-ink-900">
                    {{ __('Earlier estimates') }}
                </h2>
                <x-ui.model-note class="mt-1.5" :label="__('Each one estimated by a model, not surveyed')" />

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-left">
                        <thead>
                            <tr class="border-b border-sheet-300">
                                <th scope="col" class="py-2 pr-4 font-mono text-annotation uppercase text-ink-500">{{ __('Date') }}</th>
                                <th scope="col" class="py-2 pr-4 font-mono text-annotation uppercase text-ink-500">{{ __('Range') }}</th>
                                <th scope="col" class="py-2 pr-4 font-mono text-annotation uppercase text-ink-500">{{ __('Confidence') }}</th>
                                <th scope="col" class="py-2 font-mono text-annotation uppercase text-ink-500">{{ __('Report') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($valuationHistory as $hist)
                                @php $histRange = $hist->range(); @endphp
                                <tr class="border-b border-sheet-200 last:border-0">
                                    <td class="whitespace-nowrap py-3 pr-4 font-mono text-caption tabular-nums text-ink-700">
                                        {{ $hist->valuation_date?->format('j M Y') }}
                                    </td>
                                    <td class="whitespace-nowrap py-3 pr-4 font-mono text-caption font-medium tabular-nums text-ink-900">
                                        @if ($histRange)
                                            {{ $currency }}{{ number_format($histRange['low']) }}&ndash;{{ $currency }}{{ number_format($histRange['high']) }}
                                        @else
                                            <x-ui.not-supplied />
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap py-3 pr-4 font-mono text-caption tabular-nums text-ink-700">
                                        {{ $hist->confidence_level ?? 0 }}%
                                    </td>
                                    <td class="whitespace-nowrap py-3">
                                        <button type="button" wire:click="viewValuation({{ $hist->id }})"
                                                class="font-mono text-caption text-draft-700 underline underline-offset-2 hover:no-underline">
                                            {{ __('Open') }}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif
    @else
        <p class="mt-8 rounded-sheet border border-caution-600 bg-caution-100 px-4 py-3 text-body-s text-caution-700">
            {{ __('That property is not listed. Pick one from the search and the estimate follows.') }}
        </p>
    @endif
</div>
