<div>
    <div wire:loading class="text-sm text-gray-500" role="status">Loading territories…</div>
    <label for="territory-search">Search territories</label>
    <input id="territory-search" type="search" wire:model.live="search" autocomplete="off">
    <ul aria-live="polite">
        @forelse ($territories as $territory)
            <li wire:key="territory-{{ $territory->getKey() }}">{{ $territory->name }} ({{ $territory->code }})</li>
        @empty
            <li>No territories match this search.</li>
        @endforelse
    </ul>
</div>
