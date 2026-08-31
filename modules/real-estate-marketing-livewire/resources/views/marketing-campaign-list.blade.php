<div>
    <div wire:loading class="text-sm text-gray-500" role="status">Loading campaigns…</div>
    <input wire:model.live="search" type="search" placeholder="Search marketing campaigns">
    <ul>
        @forelse ($campaigns as $campaign)
            <li>{{ $campaign->name }} — {{ $campaign->status->value }}</li>
        @empty
            <li>No marketing campaigns found.</li>
        @endforelse
    </ul>
    {{ $campaigns->links() }}
</div>
