<div>
    <div wire:loading class="text-sm text-gray-500" role="status">Loading instructions…</div>
    <label for="instruction-search">Search instructions</label>
    <input id="instruction-search" type="search" wire:model.live="search">
    <ul>
        @forelse ($instructions as $instruction)
            <li>{{ $instruction->subject }} ({{ $instruction->status->value }})</li>
        @empty
            <li>No instructions found.</li>
        @endforelse
    </ul>
    {{ $instructions->links() }}
</div>
