<div>
    <div wire:loading class="text-sm text-gray-500" role="status">Loading valuations…</div>
    <label for="valuation-search">Search valuations</label>
    <input id="valuation-search" type="search" wire:model.live="search">
    <ul>
        @forelse ($valuations as $valuation)
            <li>{{ $valuation->subject }} ({{ $valuation->status->value }})</li>
        @empty
            <li>No valuations found.</li>
        @endforelse
    </ul>
    {{ $valuations->links() }}
</div>
