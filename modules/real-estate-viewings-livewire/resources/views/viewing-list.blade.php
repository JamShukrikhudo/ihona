<div>
    <label for="viewing-search">Search viewings</label>
    <input id="viewing-search" type="search" wire:model.live="search">
    @if ($error)
        <p role="alert">{{ $error }}</p>
    @endif
    <ul>
        @forelse ($viewings as $viewing)
            <li wire:key="viewing-{{ $viewing->getKey() }}">
                {{ $viewing->subject }} ({{ $viewing->status->value }})
                @if ($viewing->status->value === 'requested')
                    <button type="button" wire:click="confirmViewing({{ $viewing->getKey() }})">Confirm</button>
                @endif
                @if (in_array($viewing->status->value, ['requested', 'confirmed'], true))
                    <button type="button" wire:click="cancelViewing({{ $viewing->getKey() }})">Cancel</button>
                @endif
                @if ($viewing->status->value === 'confirmed')
                    <button type="button" wire:click="markNoShow({{ $viewing->getKey() }})">Mark no-show</button>
                @endif
            </li>
        @empty
            <li>No viewings found.</li>
        @endforelse
    </ul>
    {{ $viewings->links() }}
</div>
