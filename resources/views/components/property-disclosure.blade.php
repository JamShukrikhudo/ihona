@props(['property'])

@php
    $band = $property->energyBand();
    $score = $property->energyScore();
    // epc is a free-form JSON column, so a value like "n/a" or "2026-13-45"
    // would otherwise throw and take the whole public page down.
    $assessed = rescue(
        fn () => filled($raw = data_get($property->epc, 'assessment_date'))
            ? \Illuminate\Support\Carbon::parse($raw)
            : null,
        null,
        report: false
    );
    $daysListed = $property->daysListed();
    $perSqFt = $property->pricePerSquareFootForHumans();
    $energyCost = $property->annualEnergyCost();

    // Where a fact came from and when. A dated source outperforms any trust
    // badge and costs nothing but a join — and where the record holds nothing,
    // the row says so rather than leaving a reader to assume.
    $facts = [
        [
            'label' => __('Energy'),
            'source' => $assessed
                ? __('Certificate, assessed :date', ['date' => $assessed->format('j M Y')])
                : __('Energy Performance Certificate'),
            'band' => $band,
            'value' => $band ? (string) ($score ?? '') : null,
        ],
        [
            'label' => __('Floor area'),
            'source' => __('As marketed'),
            'value' => $property->area_sqft ? number_format((float) $property->area_sqft).' '.__('sq ft') : null,
        ],
        [
            'label' => $property->pricePerSquareFootLabel(),
            'source' => __('Derived from price and floor area'),
            'value' => $perSqFt,
        ],
        [
            'label' => __('Built'),
            'source' => __('As recorded'),
            'value' => $property->year_built ? (string) $property->year_built : null,
        ],
        [
            'label' => __('Days listed'),
            'source' => $property->list_date
                ? __('Listed :date', ['date' => $property->list_date->format('j M Y')])
                : __('No listing date recorded'),
            'value' => $daysListed !== null
                ? ($daysListed === 0 ? __('Today') : trans_choice(':count day|:count days', $daysListed, ['count' => $daysListed]))
                : null,
        ],
        [
            'label' => __('Tenure'),
            'source' => __('As recorded'),
            'value' => $property->tenureForHumans(),
        ],
        [
            'label' => __('Council tax'),
            'source' => __('Band as recorded, not the bill'),
            'value' => filled($property->council_tax_band)
                ? __('Band :band', ['band' => strtoupper($property->council_tax_band)])
                : null,
        ],
        [
            'label' => __('Service charge'),
            'source' => __('As recorded'),
            'value' => $property->annualCostForHumans($property->service_charge),
        ],
        [
            'label' => __('Ground rent'),
            'source' => __('As recorded'),
            'value' => $property->annualCostForHumans($property->ground_rent),
        ],
        [
            'label' => __('Energy cost'),
            // A band is not a bill: without a costed certificate this stays
            // empty rather than being guessed from the rating.
            'source' => $energyCost !== null
                ? __('Estimated on the certificate')
                : __('Not costed on the certificate'),
            'value' => $energyCost !== null
                ? __(':amount a year', ['amount' => $property->currencySymbol().number_format($energyCost)])
                : null,
        ],
        [
            'label' => __('Transit'),
            'source' => __('Walk Score'),
            'value' => $property->transit_score !== null ? ((int) $property->transit_score).'/100' : null,
        ],
    ];
@endphp

<section {{ $attributes->class('rounded-sheet border border-sheet-300 bg-sheet-000') }}
         aria-labelledby="disclosure-heading">
    <div class="flex items-center justify-between gap-3 border-b border-sheet-300 px-4 py-3">
        <h2 id="disclosure-heading" class="font-mono text-annotation uppercase text-ink-400">
            {{ __('What the record holds') }}
        </h2>
    </div>

    <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($facts as $fact)
            <div class="border-b border-sheet-300 px-4 py-3 last:border-b-0 sm:[&:nth-last-child(-n+2)]:border-b-0">
                <dt class="font-mono text-[9.5px] uppercase tracking-[0.08em] text-ink-400">
                    {{ $fact['label'] }}
                </dt>
                <dd class="mt-1 flex items-center gap-1.5 font-mono text-body-s font-medium tabular-nums text-ink-900">
                    @if (($fact['band'] ?? null))
                        <x-ui.epc-band :band="$fact['band']" :score="$score" />
                    @endif

                    @if ($fact['value'] !== null && $fact['value'] !== '')
                        {{ $fact['value'] }}
                    @elseif (! ($fact['band'] ?? null))
                        <x-ui.not-supplied />
                    @endif
                </dd>
                <p class="mt-1 text-caption text-ink-400">{{ $fact['source'] }}</p>
            </div>
        @endforeach
    </dl>
</section>
