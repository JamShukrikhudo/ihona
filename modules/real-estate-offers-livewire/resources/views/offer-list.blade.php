<div>
    <div wire:loading class="text-sm text-gray-500" role="status">Loading offers…</div>
    <label for="offer-search">Search offers</label>
    <input id="offer-search" type="search" wire:model.live="search">
    <ul>
        @forelse ($offers as $offer)
            <li>{{ $offer->subject }} ({{ $offer->status->value }})
                @if ($offer->status->value === 'draft') <button type="button" wire:click="submitOffer({{ $offer->id }})">Submit</button> @endif
                @if (in_array($offer->status->value, ['submitted', 'countered'], true)) <button type="button" wire:click="acceptOffer({{ $offer->id }})">Accept</button> <button type="button" wire:click="rejectOffer({{ $offer->id }})">Reject</button> @endif
            </li>
        @empty
            <li>No offers found.</li>
        @endforelse
    </ul>
    {{ $offers->links() }}
</div>
