<div>
    <div wire:loading class="text-sm text-gray-500" role="status">Loading sales progressions…</div>
    <input wire:model.live="search" type="search" placeholder="Search sales progressions">
    <ul>
        @forelse ($progressions as $progression)
            <li>{{ $progression->subject }} — {{ $progression->status->value }}</li>
        @empty
            <li>No sales progressions found.</li>
        @endforelse
    </ul>
    {{ $progressions->links() }}
</div>
