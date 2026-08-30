<div>
    <div wire:loading class="text-sm text-gray-500" role="status">Loading valuations…</div>
    <label for="valuation-search">Search valuations</label>
    <input id="valuation-search" type="search" wire:model.live="search">
    @if ($error)
        <p role="alert">{{ $error }}</p>
    @endif
    <label for="valuation-scheduled-at">Schedule for</label>
    <input id="valuation-scheduled-at" type="datetime-local" wire:model="scheduledAt">
    <label for="valuation-amount">Completion amount</label>
    <input id="valuation-amount" type="number" min="0" wire:model="valuedAmount">
    <label for="valuation-conversion-type">Conversion type</label>
    <input id="valuation-conversion-type" type="text" wire:model="conversionType" maxlength="80">
    <ul>
        @forelse ($valuations as $valuation)
            <li wire:key="valuation-{{ $valuation->getKey() }}">
                {{ $valuation->subject }} ({{ $valuation->status->value }})
                @if ($valuation->status->value === 'draft')
                    <button type="button" wire:click="scheduleValuation({{ $valuation->getKey() }})">Schedule</button>
                @endif
                @if ($valuation->status->value === 'scheduled')
                    <button type="button" wire:click="completeValuation({{ $valuation->getKey() }})">Complete</button>
                @endif
                @if ($valuation->status->value === 'completed')
                    <button type="button" wire:click="convertValuation({{ $valuation->getKey() }})">Convert</button>
                @endif
            </li>
        @empty
            <li>No valuations found.</li>
        @endforelse
    </ul>
    {{ $valuations->links() }}
</div>
