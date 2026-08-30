<section aria-label="Rental yield calculator" class="space-y-4">
    <div>
        <h2 class="text-lg font-semibold">Rental yield estimate</h2>
        <p class="text-sm text-gray-600">Estimate gross and net rental yield. Estimate only; vacancies, taxes, financing, maintenance, and local costs are not fully modelled.</p>
    </div>
    <div class="grid gap-4 md:grid-cols-3">
        <label>Property value <input type="number" min="0.01" step="0.01" wire:model="propertyValue"></label>
        <label>Annual rental income <input type="number" min="0" step="0.01" wire:model="annualRentalIncome"></label>
        <label>Annual expenses <input type="number" min="0" step="0.01" wire:model="annualExpenses"></label>
    </div>
    <button type="button" wire:click="calculateRentalYield">Calculate rental yield</button>
    @if ($error)<p role="alert" class="text-red-600">{{ $error }}</p>@endif
    @if ($result)
        <dl>
            <dt>Gross yield</dt><dd>{{ number_format((float) $result['gross_yield'], 2) }}%</dd>
            <dt>Net yield</dt><dd>{{ number_format((float) $result['net_yield'], 2) }}%</dd>
            <dt>Expense ratio</dt><dd>{{ number_format((float) $result['expense_ratio'], 2) }}%</dd>
        </dl>
        <button type="button" wire:click="resetCalculation">Reset</button>
    @endif
</section>
