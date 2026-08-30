<section aria-label="Saved properties">
    <h2>Saved properties</h2>
    <label for="wishlist-search">Search saved properties</label>
    <input id="wishlist-search" type="search" wire:model.live="search">
    <label for="wishlist-sort">Sort by</label>
    <select id="wishlist-sort" wire:model.live="sortBy">
        <option value="created_at">Recently saved</option>
        <option value="title">Title</option>
        <option value="price">Price</option>
        <option value="address">Address</option>
    </select>
    @if ($removed)<p role="status">{{ $removed }}</p>@endif
    <p aria-live="polite">{{ $totalFavorites }} saved properties.</p>
    <ul aria-live="polite">
        @forelse ($properties as $property)
            <li wire:key="saved-property-{{ $property->getKey() }}">
                <span>{{ $property->title ?: $property->address }}</span>
                @if ($property->price !== null)<span>{{ $property->price }}</span>@endif
                <button type="button" wire:click="removeFavorite({{ $property->getKey() }})">Remove</button>
            </li>
        @empty
            <li>No saved properties yet. Search properties to add one to your shortlist.</li>
        @endforelse
    </ul>
    {{ $properties->links() }}
</section>
