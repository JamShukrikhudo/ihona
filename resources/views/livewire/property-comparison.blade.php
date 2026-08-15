@php
    // The disclosure facts become the rows. This is the one place the mono
    // tabular figures earn their keep most: two homes line up digit for digit,
    // so the difference is the thing you see rather than something you work out.
    $rows = [
        'price' => __('Price'),
        'energy' => __('Energy'),
        'area' => __('Floor area'),
        'rate' => __('£/sq ft'),
        'bedrooms' => __('Bedrooms'),
        'bathrooms' => __('Bathrooms'),
        'listed' => __('Listed'),
        'built' => __('Built'),
        'transit' => __('Transit'),
        'type' => __('Type'),
    ];
@endphp

<div class="mx-auto max-w-(--breakpoint-xl) px-4 py-band md:px-margin">
    <header>
        <p class="font-mono text-annotation uppercase text-ink-400">{{ __('Side by side') }}</p>
        <h1 class="mt-3 font-display text-h2 font-bold tracking-tight text-ink-900">
            {{ __('Compare these homes') }}
        </h1>
    </header>

    <div class="relative mt-6 max-w-reading">
        <label for="compare-search" class="sr-only">{{ __('Add a home to compare') }}</label>
        <div class="flex items-center gap-2 rounded-sheet border border-sheet-300 bg-sheet-000 px-3 shadow-lift-1">
            <x-ui.icon name="search" class="size-4 shrink-0 text-ink-400" />
            <input id="compare-search" type="search" wire:model.live.debounce.300ms="searchTerm"
                   placeholder="{{ __('Add a home by address or postcode') }}"
                   class="w-full border-0 bg-transparent p-0 py-2.5 font-sans text-body-s text-ink-900 placeholder:text-sheet-400 focus:ring-0 focus:outline-none" />
        </div>

        @if (count($searchResults))
            <ul class="absolute z-10 mt-1 w-full overflow-hidden rounded-sheet border border-sheet-300 bg-sheet-000 shadow-lift-2">
                @foreach ($searchResults as $result)
                    <li>
                        <button type="button" wire:click="addProperty({{ $result->id }})"
                                class="block w-full px-3 py-2.5 text-left text-body-s text-ink-700 transition-colors duration-[160ms] hover:bg-sheet-200 hover:text-ink-900">
                            {{ $result->title }}
                        </button>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    @if (count($properties))
        {{-- Scrolls inside its own container, so a wide table never takes the
             page sideways with it. --}}
        <div class="mt-8 overflow-x-auto rounded-sheet border border-sheet-300">
            <table class="w-full min-w-[40rem] border-collapse bg-sheet-000 text-left">
                <thead>
                    <tr>
                        <th scope="col"
                            class="sticky left-0 z-10 border-b border-sheet-300 bg-sheet-100 px-4 py-3 font-mono text-annotation uppercase text-ink-400">
                            {{ __('Fact') }}
                        </th>
                        @foreach ($properties as $property)
                            <th scope="col" class="border-b border-l border-sheet-300 px-4 py-3 align-top">
                                <span class="block text-body-s font-semibold text-ink-900">{{ $property->title }}</span>
                                <button type="button" wire:click="removeProperty({{ $property->id }})"
                                        class="mt-1 font-mono text-annotation uppercase text-ink-400 transition-colors duration-[160ms] hover:text-fault-600"
                                        aria-label="{{ __('Remove :title from the comparison', ['title' => $property->title]) }}">
                                    {{ __('Remove') }}
                                </button>
                            </th>
                        @endforeach
                    </tr>
                </thead>

                <tbody class="font-mono tabular-nums text-body-s text-ink-900">
                    @foreach ($rows as $key => $label)
                        <tr class="border-b border-sheet-300 last:border-b-0">
                            <th scope="row"
                                class="sticky left-0 z-10 bg-sheet-100 px-4 py-2.5 text-left font-mono text-annotation font-normal uppercase text-ink-400">
                                {{ $label }}
                            </th>

                            @foreach ($properties as $property)
                                <td class="border-l border-sheet-300 px-4 py-2.5">
                                    @switch($key)
                                        @case('price')
                                            <span class="font-semibold">{{ $property->currencySymbol() }}{{ number_format((float) $property->price) }}</span>
                                            @if ($property->isRental())
                                                <span class="text-ink-400">{{ __('pcm') }}</span>
                                            @endif
                                            @break

                                        @case('energy')
                                            @if ($property->energyBand())
                                                <span class="inline-flex items-center gap-1.5">
                                                    <x-ui.epc-band :band="$property->energyBand()" :score="$property->energyScore()" />
                                                    {{ $property->energyScore() ?? '' }}
                                                </span>
                                            @else
                                                <x-ui.not-supplied />
                                            @endif
                                            @break

                                        @case('area')
                                            @if ($property->area_sqft)
                                                {{ number_format((float) $property->area_sqft) }} {{ __('sq ft') }}
                                            @else
                                                <x-ui.not-supplied />
                                            @endif
                                            @break

                                        @case('rate')
                                            {{ $property->pricePerSquareFootForHumans() ?? '' }}
                                            @if ($property->pricePerSquareFootForHumans() === null)
                                                <x-ui.not-supplied />
                                            @endif
                                            @break

                                        @case('listed')
                                            @php $days = $property->daysListed(); @endphp
                                            @if ($days === 0)
                                                {{ __('Today') }}
                                            @elseif ($days !== null)
                                                {{ trans_choice(':count day|:count days', $days, ['count' => $days]) }}
                                            @else
                                                <x-ui.not-supplied />
                                            @endif
                                            @break

                                        @case('transit')
                                            @if ($property->transit_score !== null)
                                                {{ (int) $property->transit_score }}/100
                                            @else
                                                <x-ui.not-supplied />
                                            @endif
                                            @break

                                        @case('type')
                                            {{ $property->property_type ? ucfirst($property->property_type) : '' }}
                                            @if (! $property->property_type)
                                                <x-ui.not-supplied />
                                            @endif
                                            @break

                                        @default
                                            @php $value = $property->{$key === 'built' ? 'year_built' : $key}; @endphp
                                            @if (filled($value))
                                                {{ $value }}
                                            @else
                                                <x-ui.not-supplied />
                                            @endif
                                    @endswitch
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="mt-8 rounded-sheet border border-dashed border-sheet-300 bg-sheet-000 p-10 text-center">
            <p class="font-display text-h4 font-bold tracking-tight text-ink-900">
                {{ __('Nothing to compare yet') }}
            </p>
            <p class="mx-auto mt-2 max-w-reading text-body-s text-ink-500">
                {{ __('Add two or three homes and their facts line up side by side.') }}
            </p>
            <div class="mt-4">
                <x-ui.button size="sm" :href="route('property.list')">{{ __('Browse homes') }}</x-ui.button>
            </div>
        </div>
    @endif
</div>
