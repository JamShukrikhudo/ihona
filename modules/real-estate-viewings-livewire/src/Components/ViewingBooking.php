<?php

declare(strict_types=1);

namespace Liberu\RealEstate\ViewingsLivewire\Components;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Viewings\Application\CreateViewing;
use Liberu\RealEstate\Viewings\Queries\AvailableViewingSlots;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class ViewingBooking extends Component
{
    #[Validate('nullable|string|max:255')]
    public string $search = '';

    public int|string|null $propertyId = null;

    public ?int $partyId = null;

    public string $date;

    public int $durationMinutes = 60;

    public ?string $selectedSlot = null;

    public string $subject = '';

    public array $access = [];

    public array $accompaniment = [];

    public ?string $error = null;

    public bool $booked = false;

    public function mount(int|string|null $propertyId = null): void
    {
        $this->propertyId = $propertyId;
        $this->date = now()->addDay()->toDateString();
    }

    public function updatedDate(): void
    {
        $this->selectedSlot = null;
        $this->booked = false;
    }

    public function book(CreateViewing $create, AvailableViewingSlots $slots): void
    {
        $this->validate([
            'propertyId' => ['nullable', 'integer'],
            'partyId' => ['nullable', 'integer'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'durationMinutes' => ['required', 'integer', 'between:15,240'],
            'selectedSlot' => ['required', 'date'],
            'subject' => ['required', 'string', 'max:255'],
            'access' => ['array'],
            'accompaniment' => ['array'],
        ]);

        $teamId = auth()->user()?->current_team_id;
        $actorId = auth()->id();
        if ($teamId === null || $actorId === null) {
            $this->error = 'A team context is required.';

            return;
        }

        $available = $slots->handle($teamId, $this->propertyId, CarbonImmutable::parse($this->date), $this->durationMinutes);
        if (! in_array($this->selectedSlot, $available, true)) {
            throw ValidationException::withMessages(['selectedSlot' => 'That viewing slot is no longer available.']);
        }

        try {
            $create->handle($teamId, $actorId, [
                'property_id' => $this->propertyId,
                'party_id' => $this->partyId,
                'subject' => $this->subject,
                'starts_at' => $this->selectedSlot,
                'ends_at' => CarbonImmutable::parse($this->selectedSlot)->addMinutes($this->durationMinutes),
                'access' => $this->access,
                'accompaniment' => $this->accompaniment,
            ]);
            $this->booked = true;
            $this->error = null;
            $this->selectedSlot = null;
        } catch (ValidationException $exception) {
            $this->error = $exception->getMessage();
            throw $exception;
        }
    }

    public function render(AvailableViewingSlots $slots): View
    {
        $teamId = auth()->user()?->current_team_id;
        $availableSlots = $teamId === null
            ? []
            : $slots->handle($teamId, $this->propertyId, CarbonImmutable::parse($this->date), $this->durationMinutes);

        return view('real-estate-viewings-livewire::viewing-booking', compact('availableSlots'));
    }
}
