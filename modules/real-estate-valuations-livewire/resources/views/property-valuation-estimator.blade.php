<div>
    <section aria-label="Property valuation estimator">
        <h2>Property valuation estimate</h2>
        <p>This explainable estimate is for guidance only; it is not a professional appraisal or financial advice.</p>
        <button type="button" wire:click="generateValuation">Generate estimate</button>

        @if ($error)
            <p role="alert">{{ $error }}</p>
        @endif

        @if ($valuation)
            <dl>
                <dt>Estimated value</dt>
                <dd>{{ number_format((float) $valuation['estimated_value'], 2) }}</dd>
                <dt>Confidence</dt>
                <dd>{{ $valuation['confidence_level'] }}%</dd>
                <dt>Market trend</dt>
                <dd>{{ $valuation['market_trend'] }}</dd>
                <dt>Estimated range</dt>
                <dd>{{ number_format((float) $valuation['price_range']['min'], 2) }} – {{ number_format((float) $valuation['price_range']['max'], 2) }}</dd>
            </dl>
            <button type="button" wire:click="resetValuation">Reset</button>
        @endif
    </section>
</div>
