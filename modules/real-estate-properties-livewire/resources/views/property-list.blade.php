<div>
    <div wire:loading class="text-sm text-gray-500" role="status">Loading properties…</div>
    <label for="property-search">Search properties</label>
    <input id="property-search" type="search" wire:model.live="search" autocomplete="off">
    <label for="property-postal-code">Postal code</label>
    <input id="property-postal-code" type="text" wire:model.live="postalCode" maxlength="20" autocomplete="postal-code">
    <label for="needs-syncing-only">
        <input id="needs-syncing-only" type="checkbox" wire:model.live="needsSyncingOnly">
        Needs syncing
    </label>
    @if ($error)
        <p role="alert">{{ $error }}</p>
    @endif
    <label for="property-type">Property type</label>
    <input id="property-type" type="text" wire:model.live="propertyType" autocomplete="off">
    <label for="min-price">Minimum price</label>
    <input id="min-price" type="number" wire:model.live="minPrice" min="0">
    <label for="max-price">Maximum price</label>
    <input id="max-price" type="number" wire:model.live="maxPrice" min="0">
    <label for="featured-only">
        <input id="featured-only" type="checkbox" wire:model.live="featuredOnly">
        Featured only
    </label>
    <ul aria-live="polite">
        @forelse ($properties as $property)
            <li wire:key="property-{{ $property->getKey() }}">
                {{ $property->address }} ({{ $property->status->value }})
                @if ($property->hasVirtualTour())
                    <span aria-label="Virtual tour available">Virtual tour</span>
                @endif
                @if ($property->status->value === 'draft')
                    <button type="button" wire:click="publish({{ $property->getKey() }})">Publish</button>
                @elseif ($property->status->value === 'available')
                    <button type="button" wire:click="markUnderOffer({{ $property->getKey() }})">Mark under offer</button>
                    <button type="button" wire:click="markSold({{ $property->getKey() }})">Mark sold</button>
                @elseif ($property->status->value === 'under_offer')
                    <button type="button" wire:click="markSold({{ $property->getKey() }})">Mark sold</button>
                @endif
                @if (in_array($property->status->value, ['draft', 'available', 'under_offer'], true))
                    <button type="button" wire:click="withdraw({{ $property->getKey() }})">Withdraw</button>
                @endif
            </li>
        @empty
            <li>No properties match this search.</li>
        @endforelse
    </ul>
</div>
