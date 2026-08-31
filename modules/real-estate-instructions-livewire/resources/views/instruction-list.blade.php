<div>
    <div wire:loading class="text-sm text-gray-500" role="status">Loading instructions…</div>
    <label for="instruction-search">Search instructions</label>
    <input id="instruction-search" type="search" wire:model.live="search">
    @if ($error)
        <p role="alert">{{ $error }}</p>
    @endif
    <ul>
        @forelse ($instructions as $instruction)
            <li wire:key="instruction-{{ $instruction->getKey() }}">
                {{ $instruction->subject }} ({{ $instruction->status->value }})
                @if ($instruction->status->value === 'draft')
                    <button type="button" wire:click="submitInstruction({{ $instruction->getKey() }})">Submit</button>
                @elseif ($instruction->status->value === 'pending_approval')
                    <button type="button" wire:click="approveInstruction({{ $instruction->getKey() }})">Approve</button>
                    <button type="button" wire:click="rejectInstruction({{ $instruction->getKey() }})">Reject</button>
                @elseif ($instruction->status->value === 'approved')
                    <button type="button" wire:click="withdrawInstruction({{ $instruction->getKey() }})">Withdraw</button>
                @endif
            </li>
        @empty
            <li>No instructions found.</li>
        @endforelse
    </ul>
    {{ $instructions->links() }}
</div>
