<div>
    <section aria-label="Property recommendations">
        <h2>Recommended properties</h2>
        <div wire:loading role="status">Loading recommendations…</div>
        @if ($error)
            <p role="alert">{{ $error }}</p>
        @elseif ($recommendations === [])
            <p>Nothing to recommend yet. Save a home or run a search and this fills up.</p>
        @else
            <ul>
                @foreach ($recommendations as $recommendation)
                    <li wire:key="recommendation-{{ $recommendation['id'] ?? $loop->index }}">
                        <span>{{ $recommendation['title'] ?? $recommendation['address'] ?? 'Property' }}</span>
                        <span>Match score: {{ $recommendation['recommendation_score'] }}</span>
                    </li>
                @endforeach
            </ul>
            @if (count($recommendations) >= $limit && count($candidates) > count($recommendations))
                <button type="button" wire:click="loadMore">Show more</button>
            @endif
        @endif
    </section>
</div>
