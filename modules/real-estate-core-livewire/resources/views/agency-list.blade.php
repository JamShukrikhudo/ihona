<div>
    <div wire:loading class="text-sm text-gray-500" role="status">Loading agencies…</div>
    <label for="agency-search">Search agencies</label>
    <input id="agency-search" type="search" wire:model.live="search" autocomplete="off">
    <ul aria-live="polite">
        @forelse ($agencies as $agency)
            <li wire:key="agency-{{ $agency->getKey() }}">{{ $agency->name }} ({{ $agency->code }})</li>
        @empty
            <li>No agencies match this search.</li>
        @endforelse
    </ul>
</div>
