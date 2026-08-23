<div>
    <label for="offer-search">Search offers</label>
    <input id="offer-search" type="search" wire:model.live="search">
    <ul>
        @forelse ($offers as $offer)
            <li>{{ $offer->subject }} ({{ $offer->status->value }})</li>
        @empty
            <li>No offers found.</li>
        @endforelse
    </ul>
    {{ $offers->links() }}
</div>
