<div>
    <div wire:loading class="text-sm text-gray-500" role="status">Loading OnTheMarket syncs…</div>
    <input wire:model.live="search" type="search" placeholder="Search OnTheMarket syncs">
    <ul>
        @forelse ($syncs as $sync)
            <li>{{ $sync->listing_id }} — {{ $sync->external_id }} — {{ $sync->status->value }}</li>
        @empty
            <li>No OnTheMarket syncs found.</li>
        @endforelse
    </ul>
    {{ $syncs->links() }}
</div>
