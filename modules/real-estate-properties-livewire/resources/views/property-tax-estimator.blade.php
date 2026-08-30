<div>
    <section aria-label="Property tax estimator">
        <h2>Property tax estimate</h2>
        <p>This calculator provides an estimate only; confirm current rates with a qualified adviser.</p>

        <label for="tax-buyer-type">Buyer type</label>
        <select id="tax-buyer-type" wire:model="buyerType">
            <option value="first_time_buyer">First-time buyer</option>
            <option value="home_mover">Home mover</option>
            <option value="additional_property">Additional property</option>
        </select>

        <label for="tax-country">Country</label>
        <input id="tax-country" type="text" wire:model="country" maxlength="80">

        <button type="button" wire:click="calculateTax">Calculate estimate</button>

        @if ($estimate)
            <dl>
                <dt>Estimated tax</dt>
                <dd>{{ number_format((float) ($estimate['total_tax'] ?? 0), 2) }}</dd>
                <dt>Additional costs</dt>
                <dd>{{ number_format((float) ($estimate['total_additional_costs'] ?? 0), 2) }}</dd>
                <dt>Total estimated cost</dt>
                <dd>{{ number_format((float) ($estimate['total_cost'] ?? $property->price), 2) }}</dd>
            </dl>
            <button type="button" wire:click="resetCalculation">Reset</button>
        @endif
    </section>
</div>
