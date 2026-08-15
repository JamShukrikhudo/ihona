@if ($investmentAnalytics)
    @php
        $prediction = $investmentAnalytics['prediction'];
        $range = $prediction['confidence'] ?? null;
        $cashFlow = $investmentAnalytics['cash_flow_analysis'] ?? null;
        $position = $investmentAnalytics['market_position'] ?? null;
    @endphp

    {{--
        Nothing in this block is a fact the record holds. Every figure is a
        model's estimate, so the whole block is fenced and labelled, and each
        number carries the band the model itself implies rather than two
        decimal places of borrowed authority.
    --}}
    <section class="mt-6 rounded-sheet border border-sheet-300 bg-sheet-100 p-4 sm:p-5"
             aria-labelledby="investment-heading">
        <h2 id="investment-heading" class="font-display text-h5 font-bold tracking-tight text-ink-900">
            {{ __('Investment estimate') }}
        </h2>

        <x-ui.model-note class="mt-1.5" :label="__('Estimated by a model, not surveyed')">
            {{ __('Not advice. Check any figure before you rely on it.') }}
        </x-ui.model-note>

        <dl class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
            <div class="rounded-sheet border border-sheet-300 bg-sheet-000 p-4">
                <dt class="font-mono text-annotation uppercase text-ink-500">{{ __('Return over 5 years') }}</dt>
                <dd class="mt-1.5">
                    @if ($range)
                        {{-- The range is the figure. The midpoint sits under it
                             in a smaller size so it cannot be read as the
                             answer on its own. --}}
                        <p class="font-display text-h4 font-bold tabular-nums tracking-tight text-ink-900">
                            {{ number_format($range['low'], 1) }}%&ndash;{{ number_format($range['high'], 1) }}%
                        </p>
                        <p class="mt-1 font-mono text-micro tabular-nums text-ink-400">
                            {{ __('midpoint :value%', ['value' => number_format($prediction['predicted_roi'], 1)]) }}
                        </p>
                    @else
                        <p class="font-display text-h4 font-bold tabular-nums tracking-tight text-ink-900">
                            {{ number_format($prediction['predicted_roi'], 1) }}%
                        </p>
                    @endif
                </dd>
            </div>

            <div class="rounded-sheet border border-sheet-300 bg-sheet-000 p-4">
                <dt class="font-mono text-annotation uppercase text-ink-500">{{ __('Risk score') }}</dt>
                <dd class="mt-1.5">
                    <p class="font-display text-h4 font-bold tabular-nums tracking-tight text-ink-900">
                        {{ number_format($prediction['risk_score'], 1) }}<span class="text-body-s text-ink-400">/10</span>
                    </p>
                    <div class="mt-2 h-1.5 w-full overflow-hidden rounded-pill bg-sheet-200">
                        <div class="h-full bg-survey-500"
                             style="width: {{ max(0, min(100, ($prediction['risk_score'] / 10) * 100)) }}%"></div>
                    </div>
                    <p class="mt-1.5 font-mono text-micro text-ink-400">
                        {{ __('It also sets the width of the range beside it.') }}
                    </p>
                </dd>
            </div>
        </dl>

        @if ($cashFlow)
            <dl class="mt-3 grid grid-cols-2 gap-x-4 gap-y-3 rounded-sheet border border-sheet-300 bg-sheet-000 p-4 sm:grid-cols-4">
                @foreach ([
                    __('Annual rent') => $currency.number_format($cashFlow['estimated_annual_rent'], 0),
                    __('Expenses') => $currency.number_format($cashFlow['estimated_expenses'], 0),
                    __('Net cash flow') => $currency.number_format($cashFlow['net_cash_flow'], 0),
                    __('Cash-on-cash') => number_format($cashFlow['cash_on_cash_return'], 1).'%',
                ] as $label => $value)
                    <div class="min-w-0">
                        <dt class="font-mono text-annotation uppercase text-ink-500">{{ $label }}</dt>
                        <dd class="mt-1 truncate font-mono text-body-s font-medium tabular-nums text-ink-900">
                            {{ $value }}
                        </dd>
                    </div>
                @endforeach
            </dl>
        @endif

        @if ($position)
            <div class="mt-3 rounded-sheet border border-sheet-300 bg-sheet-000 p-4">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <x-ui.chip tone="info">
                        {{ ucfirst(str_replace('_', ' ', $position['position'])) }}
                    </x-ui.chip>
                    {{-- Below the market is not automatically good news and
                         above it is not automatically bad, so the number is
                         stated rather than coloured green or red. --}}
                    <span class="font-mono text-caption tabular-nums text-ink-700">
                        {{ $position['price_vs_market'] >= 0 ? '+' : '' }}{{ number_format($position['price_vs_market'], 1) }}%
                        {{ __('vs market') }}
                    </span>
                </div>
                <p class="mt-2 text-body-s text-ink-700">{{ $position['competitive_advantage'] }}</p>
            </div>
        @endif

        <div class="mt-4">
            <x-ui.button variant="secondary" size="sm" wire:click="toggleInvestmentSimulation">
                {{ $showInvestmentSimulation ? __('Hide the simulator') : __('Open the simulator') }}
            </x-ui.button>
        </div>
    </section>
@endif
