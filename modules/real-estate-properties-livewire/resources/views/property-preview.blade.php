<section aria-label="Property preview">
    @if ($property)
        <h2>{{ $property['title'] ?? 'Untitled property' }}</h2>
        <p>{{ $property['location'] ?? 'Location not supplied' }}</p>
        <dl>
            <div><dt>Price</dt><dd>{{ $property['price'] ?? 'Not supplied' }}</dd></div>
            <div><dt>Description</dt><dd>{{ $property['description'] ?? 'Not supplied' }}</dd></div>
            @if (filled($property['custom_description'] ?? null))
                <div><dt>Additional notes</dt><dd>{{ $property['custom_description'] }}</dd></div>
            @endif
        </dl>
    @else
        <p>No property selected for preview.</p>
    @endif
</section>
