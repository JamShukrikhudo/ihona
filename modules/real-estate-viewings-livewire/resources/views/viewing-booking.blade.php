<div>
    <form wire:submit="book" aria-label="Book a viewing">
        <div wire:loading class="text-sm text-gray-500" role="status">Loading availability…</div>
        @if ($booked)
            <p role="status">Viewing requested.</p>
        @endif
        @if ($error)
            <p role="alert">{{ $error }}</p>
        @endif
        <label for="viewing-booking-date">Date</label>
        <input id="viewing-booking-date" type="date" wire:model.live="date">
        <label for="viewing-booking-subject">Subject</label>
        <input id="viewing-booking-subject" type="text" wire:model="subject" maxlength="255">
        <fieldset>
            <legend>Available times</legend>
            @forelse ($availableSlots as $slot)
                <label wire:key="viewing-slot-{{ $slot }}">
                    <input type="radio" wire:model="selectedSlot" value="{{ $slot }}">
                    {{ \Carbon\CarbonImmutable::parse($slot)->format('H:i') }}
                </label>
            @empty
                <p>No viewing slots are available for this date.</p>
            @endforelse
        </fieldset>
        <button type="submit">Request viewing</button>
    </form>
</div>
