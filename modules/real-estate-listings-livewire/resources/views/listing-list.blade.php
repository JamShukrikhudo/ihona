<div>
    <label for="listing-search">Search listings</label>
    <input id="listing-search" type="search" wire:model.live="search">
    <ul>
        @forelse ($listings as $listing)
            <li>{{ $listing->title }} ({{ $listing->status->value }})</li>
        @empty
            <li>No listings found.</li>
        @endforelse
    </ul>
    {{ $listings->links() }}
</div>
