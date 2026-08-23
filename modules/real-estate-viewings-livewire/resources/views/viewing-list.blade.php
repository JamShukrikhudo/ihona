<div>
    <label for="viewing-search">Search viewings</label>
    <input id="viewing-search" type="search" wire:model.live="search">
    <ul>
        @forelse ($viewings as $viewing)
            <li>{{ $viewing->subject }} ({{ $viewing->status->value }})</li>
        @empty
            <li>No viewings found.</li>
        @endforelse
    </ul>
    {{ $viewings->links() }}
</div>
