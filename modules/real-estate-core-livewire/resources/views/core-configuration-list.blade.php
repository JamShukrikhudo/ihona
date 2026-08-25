<div>
    <div wire:loading class="text-sm text-gray-500" role="status">Loading core configuration…</div>
    <label for="core-configuration-search">Search configuration</label>
    <input id="core-configuration-search" type="search" wire:model.live="search">
    <h2>Terminology</h2>
    <ul>
        @forelse ($terminology as $item)
            <li>{{ $item->key }} — {{ $item->value }}</li>
        @empty
            <li>No terminology configured.</li>
        @endforelse
    </ul>
    <h2>Status definitions</h2>
    <ul>
        @forelse ($statuses as $status)
            <li>{{ $status->entity }}:{{ $status->key }} — {{ $status->label }}</li>
        @empty
            <li>No status definitions configured.</li>
        @endforelse
    </ul>
</div>
