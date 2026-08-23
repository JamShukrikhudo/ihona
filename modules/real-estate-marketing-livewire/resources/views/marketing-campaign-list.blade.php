<div>
    <input wire:model.live="search" type="search" placeholder="Search marketing campaigns">
    <ul>
        @foreach ($campaigns as $campaign)
            <li>{{ $campaign->name }} — {{ $campaign->status->value }}</li>
        @endforeach
    </ul>
    {{ $campaigns->links() }}
</div>
