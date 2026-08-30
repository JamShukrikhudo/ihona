<div>
    <div wire:loading class="text-sm text-gray-500" role="status">Loading listings…</div>
    <label for="listing-search">Search listings</label>
    <input id="listing-search" type="search" wire:model.live="search">
    @if ($error)
        <p role="alert">{{ $error }}</p>
    @endif
    <ul>
        @forelse ($listings as $listing)
            <li wire:key="listing-{{ $listing->getKey() }}">
                {{ $listing->title }} ({{ $listing->status->value }})
                @if ($listing->status->value === 'draft')
                    <button type="button" wire:click="markReady({{ $listing->getKey() }})">Mark ready</button>
                @elseif (in_array($listing->status->value, ['ready', 'suspended'], true))
                    <button type="button" wire:click="publish({{ $listing->getKey() }})">Publish</button>
                @elseif ($listing->status->value === 'published')
                    <button type="button" wire:click="suspend({{ $listing->getKey() }})">Suspend</button>
                @endif
                @if (in_array($listing->status->value, ['ready', 'published', 'suspended'], true))
                    <button type="button" wire:click="withdraw({{ $listing->getKey() }})">Withdraw</button>
                @endif
            </li>
        @empty
            <li>No listings found.</li>
        @endforelse
    </ul>
    {{ $listings->links() }}
</div>
