<div>
    <input wire:model.live="search" type="search" placeholder="Search sales progressions">
    <ul>
        @foreach ($progressions as $progression)
            <li>{{ $progression->subject }} — {{ $progression->status->value }}</li>
        @endforeach
    </ul>
    {{ $progressions->links() }}
</div>
