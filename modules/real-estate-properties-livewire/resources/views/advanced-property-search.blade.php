<div>
    <form wire:submit="applyFilters" class="space-y-3" aria-label="Advanced property search">
        <div wire:loading class="text-sm text-gray-500" role="status">Loading properties…</div>
        <label for="advanced-property-search">Search</label>
        <input id="advanced-property-search" type="search" wire:model.live="search" autocomplete="off">
        <label for="advanced-postal-code">Postal code</label>
        <input id="advanced-postal-code" type="text" wire:model="postalCode" maxlength="20" autocomplete="postal-code">
        <label for="advanced-min-price">Minimum price</label>
        <input id="advanced-min-price" type="number" wire:model="minPrice" min="0">
        <label for="advanced-max-price">Maximum price</label>
        <input id="advanced-max-price" type="number" wire:model="maxPrice" min="0">
        <label for="advanced-property-type">Property type</label>
        <input id="advanced-property-type" type="text" wire:model="propertyType" maxlength="40">
        <label for="advanced-property-category">Category</label>
        <select id="advanced-property-category" wire:model="propertyCategoryId">
            <option value="">Any category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->getKey() }}">{{ $category->name }}</option>
            @endforeach
        </select>
        <label for="advanced-property-template">Listing template</label>
        <select id="advanced-property-template" wire:model="propertyTemplateId">
            <option value="">Any template</option>
            @foreach ($templates as $template)
                <option value="{{ $template->getKey() }}">{{ $template->name }}</option>
            @endforeach
        </select>
        <label for="advanced-country">Country</label>
        <input id="advanced-country" type="text" wire:model="country" maxlength="2">
        <label for="advanced-latitude">Latitude</label>
        <input id="advanced-latitude" type="number" wire:model="latitude" min="-90" max="90" step="any">
        <label for="advanced-longitude">Longitude</label>
        <input id="advanced-longitude" type="number" wire:model="longitude" min="-180" max="180" step="any">
        <label for="advanced-radius">Radius (km)</label>
        <input id="advanced-radius" type="number" wire:model="radius" min="0.1" max="500" step="any">
        <fieldset>
            <legend>Amenities</legend>
            @foreach (['garden' => 'Garden', 'parking' => 'Parking', 'balcony' => 'Balcony', 'garage' => 'Garage', 'fireplace' => 'Fireplace'] as $amenity => $label)
                <label for="advanced-amenity-{{ $amenity }}">
                    <input id="advanced-amenity-{{ $amenity }}" type="checkbox" value="{{ $amenity }}" wire:model="selectedAmenities">
                    {{ $label }}
                </label>
            @endforeach
        </fieldset>
        <label for="advanced-featured">
            <input id="advanced-featured" type="checkbox" wire:model="featuredOnly">
            Featured only
        </label>
        <label for="advanced-needs-syncing">
            <input id="advanced-needs-syncing" type="checkbox" wire:model="needsSyncingOnly">
            Needs syncing
        </label>
        <button type="submit">Apply filters</button>
    </form>

    <ul aria-live="polite">
        @forelse ($properties as $property)
            <li wire:key="advanced-property-{{ $property->getKey() }}">
                {{ $property->title ?: $property->address }}
            </li>
        @empty
            <li>No properties match these filters.</li>
        @endforelse
    </ul>
</div>
