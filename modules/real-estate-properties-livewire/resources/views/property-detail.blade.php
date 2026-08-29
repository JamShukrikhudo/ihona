<div>
    <article aria-label="Property detail">
        <header>
            <h1>{{ $property->title ?: $property->address }}</h1>
            <p>{{ $property->address }}</p>
            @if ($property->price !== null)
                <p>{{ $property->currency ?: 'GBP' }} {{ number_format((float) $property->price, 2) }}</p>
            @endif
        </header>

        @if ($gallery !== [])
            <section aria-label="Property gallery">
                <h2>Gallery</h2>
                <div class="aspect-3/2 overflow-hidden">
                    @foreach ($gallery as $item)
                        <figure wire:key="gallery-item-{{ $loop->index }}">
                            <img src="{{ $item->url }}" alt="{{ $item->alt() }}" loading="lazy" class="h-full w-full {{ $item->isPlan() ? 'object-contain' : 'object-cover' }}">
                            <figcaption>{{ $item->caption ?? ucfirst($item->kind) }}@if ($item->staged) — Virtually staged @endif</figcaption>
                        </figure>
                    @endforeach
                </div>
            </section>
        @endif

        <section aria-label="Property disclosure">
            <h2>Property facts</h2>
            <dl>
                @foreach ($facts as $fact)
                    <div wire:key="property-fact-{{ $loop->index }}">
                        <dt>{{ $fact['label'] }}</dt>
                        <dd>{{ $fact['value'] ?? 'Not supplied' }}</dd>
                        <small>{{ $fact['source'] }}</small>
                    </div>
                @endforeach
            </dl>
        </section>

        @if ($property->floor_plan_image)
            <figure>
                <img src="{{ $property->floor_plan_image }}" alt="Floor plan for {{ $property->title ?: $property->address }}" loading="lazy">
            </figure>
        @endif

        @if ($property->hasVirtualTour())
            <button type="button" wire:click="toggleVirtualTour">{{ $showVirtualTour ? 'Hide virtual tour' : 'Show virtual tour' }}</button>
            @if ($showVirtualTour)
                {!! $property->getVirtualTourEmbed() !!}
            @endif
        @endif

        <button type="button" wire:click="toggleFavorite">{{ $isFavorited ? 'Unfavorite' : 'Favorite' }}</button>
        <button type="button" wire:click="requestViewing">Book a viewing</button>
    </article>
</div>
