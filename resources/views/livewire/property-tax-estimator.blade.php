<div class="property-tax-estimator">
    <section class="rounded-sheet border border-sheet-300 bg-sheet-100 p-4 sm:p-5"
             aria-labelledby="tax-estimator-heading">
        <h2 id="tax-estimator-heading" class="font-display text-h5 font-bold tracking-tight text-ink-900">
            {{ __('Property tax estimator') }}
        </h2>

        {{-- Every figure below is arithmetic on a rate table, not a fact the
             record holds, so the block is fenced the same way the investment
             estimate is. --}}
        <x-ui.model-note class="mt-1.5" :label="__('Estimated from published rates, not a quote')">
            {{ __('Check any figure with a solicitor before you rely on it.') }}
        </x-ui.model-note>

        @if (! $showResults)
            <p class="mt-4 text-body-s text-ink-700">
                {{ __('Taxes and costs on a purchase price of') }}
                <strong class="font-mono tabular-nums text-ink-900">{{ $property->currencySymbol() }}{{ number_format($property->price, 2) }}</strong>@if ($country === 'UK' || $country === 'GB'), {{ __('United Kingdom') }}@endif.
            </p>

            @if ($country === 'UK' || $country === 'GB')
                @php
                    $buyerHints = [
                        'first_time_buyer' => __('First-time buyers may pay reduced stamp duty up to £500,000.'),
                        'home_mover' => __('Standard stamp duty rates apply.'),
                        'additional_property' => __('A 3% surcharge applies to second homes and buy-to-let.'),
                    ];
                @endphp

                <x-ui.field
                    id="buyerType"
                    class="mt-4"
                    :label="__('Buyer type')"
                    :hint="$buyerHints[$buyerType] ?? null"
                    :error="$errors->first('buyerType')"
                >
                    <x-ui.control
                        as="select"
                        id="buyerType"
                        wire:model.live="buyerType"
                        aria-describedby="buyerType-hint"
                        :invalid="$errors->has('buyerType')"
                    >
                        <option value="first_time_buyer">{{ __('First-time buyer') }}</option>
                        <option value="home_mover">{{ __('Home mover') }}</option>
                        <option value="additional_property">{{ __('Additional property / buy-to-let') }}</option>
                    </x-ui.control>
                </x-ui.field>
            @endif

            <div class="mt-4">
                {{-- Secondary: "Book a viewing" is this page's one primary. --}}
                <x-ui.button variant="secondary" wire:click="calculateTax">
                    {{ __('Estimate the taxes') }}
                </x-ui.button>
            </div>
        @else
            <div class="mt-4 flex flex-wrap items-center justify-between gap-2">
                <h3 class="font-display text-body-l font-bold tracking-tight text-ink-900">
                    {{ __('Estimate') }}
                </h3>
                <x-ui.chip tone="info">{{ $estimatedTax['country'] }}</x-ui.chip>
            </div>

            <div class="mt-3 overflow-hidden rounded-sheet border border-sheet-300 bg-sheet-000">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-sheet-300">
                            <th scope="col" class="px-4 py-3 text-left font-mono text-annotation uppercase text-ink-500">
                                {{ __('Item') }}
                            </th>
                            <th scope="col" class="px-4 py-3 text-right font-mono text-annotation uppercase text-ink-500">
                                {{ __('Amount') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($estimatedTax['breakdown'] as $label => $amount)
                            {{-- The total is the row a reader is looking for, so it
                                 is weighted rather than coloured. --}}
                            @php $isTotal = str_contains($label, 'Total'); @endphp
                            <tr class="border-t border-sheet-300 {{ $isTotal ? 'bg-sheet-200' : '' }}">
                                <td class="px-4 py-3 text-body-s {{ $isTotal ? 'font-semibold text-ink-900' : 'text-ink-700' }}">
                                    {{ $label }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-body-s tabular-nums {{ $isTotal ? 'font-semibold text-ink-900' : 'text-ink-700' }}">
                                    {{ $property->currencySymbol() }}{{ number_format($amount, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <dl class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div class="rounded-sheet border border-sheet-300 bg-sheet-000 p-4">
                    <dt class="font-mono text-annotation uppercase text-ink-500">{{ __('Total tax') }}</dt>
                    <dd class="mt-1.5 font-display text-h4 font-bold tabular-nums tracking-tight text-ink-900">
                        {{ $property->currencySymbol() }}{{ number_format($estimatedTax['total_tax'], 2) }}
                    </dd>
                </div>
                <div class="rounded-sheet border border-sheet-300 bg-sheet-000 p-4">
                    <dt class="font-mono text-annotation uppercase text-ink-500">{{ __('Effective rate') }}</dt>
                    <dd class="mt-1.5 font-display text-h4 font-bold tabular-nums tracking-tight text-ink-900">
                        {{ $estimatedTax['effective_tax_rate'] }}%
                    </dd>
                </div>
            </dl>

            <div class="mt-4">
                <x-ui.button variant="secondary" wire:click="resetCalculation">
                    {{ __('Estimate again') }}
                </x-ui.button>
            </div>
        @endif
    </section>
</div>
