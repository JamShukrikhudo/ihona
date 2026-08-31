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

        @if ($property->model3dUrl())
            <section aria-label="3D property model">
                <button type="button" wire:click="toggle3dModel">
                    {{ $show3dModel ? 'Hide 3D model' : 'Show 3D model' }}
                </button>
                @if ($show3dModel)
                    <model-viewer
                        src="{{ $property->model3dUrl() }}"
                        alt="3D model of {{ $property->title ?: $property->address }}"
                        loading="lazy"
                        camera-controls
                        reveal="manual"
                        class="h-96 w-full"
                    ></model-viewer>
                @endif
            </section>
        @endif

        @if ($videoUrl)
            <section aria-label="Property video">
                <h2>Property video</h2>
                <video class="h-full w-full" controls preload="none" aria-label="Video tour of {{ $property->title ?: $property->address }}">
                    <source src="{{ $videoUrl }}">
                    Your browser cannot play this video.
                </video>
            </section>
        @endif

        <button type="button" wire:click="toggleFavorite">{{ $isFavorited ? 'Unfavorite' : 'Favorite' }}</button>
        <button type="button" wire:click="requestViewing">Book a viewing</button>
        @if ($property->live_tour_available)
            <button type="button" wire:click="openScheduleLiveTourModal">Book a live virtual tour</button>
        @endif

        @if ($showScheduleLiveTourModal)
            <section aria-label="Schedule live virtual tour">
                <h2>Schedule a live virtual tour</h2>
                <form wire:submit="scheduleLiveTour">
                    <label for="tour-date">Date</label>
                    <input id="tour-date" type="date" wire:model="tourDate" required>
                    <label for="tour-time">Time</label>
                    <input id="tour-time" type="time" wire:model="tourTime" required>
                    <label for="tour-notes">Notes</label>
                    <textarea id="tour-notes" wire:model="tourNotes" maxlength="500"></textarea>
                    @error('tourDate') <p role="alert">{{ $message }}</p> @enderror
                    @error('tourTime') <p role="alert">{{ $message }}</p> @enderror
                    <button type="submit">Schedule tour</button>
                    <button type="button" wire:click="closeScheduleLiveTourModal">Cancel</button>
                </form>
            </section>
        @endif
    </article>
</div>
