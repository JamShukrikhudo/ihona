<div>
    <div wire:loading class="text-sm text-gray-500" role="status">Loading Rightmove syncs…</div>
    <input wire:model.live="search" type="search" placeholder="Search Rightmove syncs">
    <ul>
        @forelse ($syncs as $sync)
            <li>{{ $sync->listing_id }} — {{ $sync->external_id }} — {{ $sync->status->value }}</li>
        @empty
            <li>No Rightmove syncs found.</li>
        @endforelse
    </ul>
    {{ $syncs->links() }}
</div>
