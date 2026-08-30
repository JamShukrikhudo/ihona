<div>
    <section aria-label="Mortgage calculator">
        <h2>Mortgage estimate</h2>
        <p>This is an estimate only; actual lender offers, fees, taxes, and rates vary.</p>
        <label for="mortgage-property-price">Property price</label>
        <input id="mortgage-property-price" type="number" min="0.01" wire:model="propertyPrice">
        <label for="mortgage-loan-amount">Loan amount</label>
        <input id="mortgage-loan-amount" type="number" min="0.01" wire:model="loanAmount">
        <label for="mortgage-interest-rate">Annual interest rate</label>
        <input id="mortgage-interest-rate" type="number" min="0" max="100" step="0.01" wire:model="interestRate">
        <label for="mortgage-term">Loan term in years</label>
        <input id="mortgage-term" type="number" min="1" max="50" wire:model="loanTermYears">
        <button type="button" wire:click="calculateMortgage">Calculate estimate</button>
        @if ($error)
            <p role="alert">{{ $error }}</p>
        @endif
        @if ($result)
            <dl>
                <dt>Monthly payment</dt>
                <dd>{{ number_format((float) $result['monthly_payment'], 2) }}</dd>
                <dt>Total interest</dt>
                <dd>{{ number_format((float) $result['total_interest'], 2) }}</dd>
                <dt>Loan to value</dt>
                <dd>{{ number_format((float) $result['loan_to_value'], 2) }}%</dd>
            </dl>
            <button type="button" wire:click="resetCalculation">Reset</button>
        @endif
    </section>
</div>
