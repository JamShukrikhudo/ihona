<div>
    <div wire:loading class="text-sm text-gray-500" role="status">Loading properties…</div>
    <label for="property-search">Search properties</label>
    <input id="property-search" type="search" wire:model.live="search" autocomplete="off">
    <ul aria-live="polite">
        @forelse ($properties as $property)
            <li wire:key="property-{{ $property->getKey() }}">{{ $property->address }}</li>
        @empty
            <li>No properties match this search.</li>
        @endforelse
    </ul>
</div>
