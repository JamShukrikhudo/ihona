<div>
    <div wire:loading class="text-sm text-gray-500" role="status">Loading matching profiles…</div>
    <label for="matching-search">Search matching profiles</label>
    <input id="matching-search" type="search" wire:model.live="search">
    <ul>
        @forelse ($profiles as $profile)
            <li>{{ $profile->subject }} (score {{ $profile->score }})</li>
        @empty
            <li>No matching profiles found.</li>
        @endforelse
    </ul>
    {{ $profiles->links() }}
</div>
