<div>
    <input wire:model.live="search" type="search" placeholder="Search Zoopla syncs">
    <ul>
        @foreach ($syncs as $sync)
            <li>{{ $sync->listing_id }} — {{ $sync->external_id }} — {{ $sync->status->value }}</li>
        @endforeach
    </ul>
    {{ $syncs->links() }}
</div>
