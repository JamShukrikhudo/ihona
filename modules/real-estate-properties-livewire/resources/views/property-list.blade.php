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
    <select id="property-type" wire:model.live="propertyType">
        <option value="">Any property type</option>
        @foreach ($propertyTypes as $type => $label)
            <option value="{{ $type }}">{{ $label }}</option>
        @endforeach
    </select>
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
                <button type="button" wire:click="toggleFavorite({{ $property->getKey() }})">
                    {{ $property->favorites->contains(fn ($favorite) => (string) $favorite->user_id === (string) auth()->id()) ? 'Unfavorite' : 'Favorite' }}
                </button>
                <button type="button" wire:click="showSimilar({{ $property->getKey() }})">Similar</button>
                @if ($property->isHmo())
                    <span aria-label="House in multiple occupation">HMO</span>
                @endif
                @if ($property->hasActiveInsurance())
                    <span aria-label="Active insurance">Insured</span>
                @endif
                @if ($property->hasVirtualTour())
                    <span aria-label="Virtual tour available">Virtual tour</span>
                @endif
                @if ($property->floor_plan_image)
                    <a href="{{ $property->floor_plan_image }}" rel="noopener" target="_blank">Floor plan</a>
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
    @if ($similarProperties !== [])
        <section aria-label="Similar properties">
            <h2>Similar properties</h2>
            <ul>
                @foreach ($similarProperties as $similar)
                    <li wire:key="similar-property-{{ $similar['id'] }}">{{ $similar['title'] }}</li>
                @endforeach
            </ul>
        </section>
    @endif
</div>
