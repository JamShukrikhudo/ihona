<div>
    <div wire:loading class="text-sm text-gray-500" role="status">Loading branches…</div>
    <label for="branch-search">Search branches</label>
    <input id="branch-search" type="search" wire:model.live="search" autocomplete="off">
    <ul aria-live="polite">
        @forelse ($branches as $branch)
            <li wire:key="branch-{{ $branch->getKey() }}">{{ $branch->name }} ({{ $branch->code }})</li>
        @empty
            <li>No branches match this search.</li>
        @endforelse
    </ul>
</div>
