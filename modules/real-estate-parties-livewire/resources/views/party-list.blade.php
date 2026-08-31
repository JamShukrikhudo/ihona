<div>
    <div wire:loading class="text-sm text-gray-500" role="status">Loading parties…</div>
    <label for="party-search">Search parties</label>
    <input id="party-search" type="search" wire:model.live="search" autocomplete="off">
    <ul aria-live="polite">
        @forelse ($parties as $party)
            <li wire:key="party-{{ $party->getKey() }}">{{ $party->name }}</li>
        @empty
            <li>No parties match this search.</li>
        @endforelse
    </ul>
</div>
