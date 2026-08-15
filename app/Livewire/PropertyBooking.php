<?php

namespace App\Livewire;

use App\Mail\ViewingBooked;
use Exception;
use Illuminate\Support\Facades\Mail;
use Log;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use App\Models\Property;
use App\Models\Booking;
use App\Models\User;
use App\Events\BookingCreated;
use App\Notifications\BookingNotification;
use App\Services\CalendarIntegrationService;
use Carbon\Carbon;

class PropertyBooking extends Component
{
    public $propertyId;
    public $selectedDate;
    public $selectedTime;
    public $userName;
    public $userEmail;
    public $userContact;
    public $notes;
    public $availableDates = [];
    public $availableTimeSlots = [];
    public $bookingConfirmed = false;
    // Not a flash: a flash set in a Livewire action survives into the next full
    // page load, so the viewing confirmation turned up on whatever page the
    // visitor opened next.
    public $confirmation = null;
    public $confirmedBookingId = null;
    public $googleCalendarUrl = null;
    public $outlookCalendarUrl = null;

    protected $messages = [
        'selectedDate.required' => 'Pick a date that suits you.',
        'selectedDate.after_or_equal' => 'Pick a date from today onwards.',
        'selectedTime.required' => 'Pick a time on that day.',
        'userName.required' => 'Add your name so the agent knows who to expect.',
        'userEmail.email' => 'Add the part after the @ so we can send the confirmation.',
        'userContact.required' => 'Add a phone number in case the agent needs to reach you on the day.',
        'notes.max' => 'Keep it under 1,000 characters and tell us the rest on the day.',
    ];

    protected $rules = [
        'selectedDate' => 'required|date|after_or_equal:today',
        'selectedTime' => 'required|string',
        'userName' => 'required|string|max:255',
        'userEmail' => 'nullable|email|max:255',
        'userContact' => 'required|string|max:255',
        'notes' => 'nullable|string|max:1000',
    ];

    public function mount($propertyId)
    {
        $this->propertyId = $propertyId;
        $property = Property::with('team')->findOrFail($this->propertyId);
        $this->availableDates = $property->availableViewingDates();
    }

    public function updatedSelectedDate($value)
    {
        $this->selectedTime = null;
        if ($value) {
            $this->availableTimeSlots = $this->getAvailableTimeSlots($value);
        } else {
            $this->availableTimeSlots = [];
        }
    }

    private function getAvailableTimeSlots($date)
    {
        // The model owns this now. Two copies of "what hours can be booked"
        // disagreed: the date picker dropped a whole day on the first booking,
        // so the eight remaining slots this returns were unreachable.
        return Property::findOrFail($this->propertyId)->availableViewingSlots($date);
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;
        $this->validate(['selectedDate' => $this->rules['selectedDate']]);
        $this->updatedSelectedDate($date);
    }

    public function bookViewing()
    {
        $this->validate();

        try {
            // The same method that produced the list the visitor chose from.
            // This called getAvailableDates(), which does not exist on Property
            // — the only one is a private method on a different component — so
            // every submission threw BadMethodCallException, the catch turned
            // it into "an unexpected error occurred", and no viewing was ever
            // booked. The route was broken until recently, so nothing exercised
            // it.
            $property = Property::findOrFail($this->propertyId);

            if (!in_array($this->selectedDate, $property->availableViewingDates())) {
                throw new Exception('Selected date is no longer available.');
            }

            if (!in_array($this->selectedTime, $property->availableViewingSlots($this->selectedDate))) {
                throw new Exception('Selected time slot is no longer available.');
            }

            // A missing staff role must not stop a visitor booking. Spatie
            // throws RoleDoesNotExist rather than returning nothing, and the
            // booking is perfectly valid unassigned.
            $defaultStaffId = rescue(
                fn () => User::role('staff')->first()?->id,
                null,
                report: false
            );

            $booking = Booking::create([
                'property_id' => $this->propertyId,
                'date' => Carbon::parse($this->selectedDate)->format('Y-m-d'),
                'time' => $this->selectedTime,
                'user_id' => auth()->id(),
                'name' => $this->userName,
                'contact' => $this->userContact,
                // Collected and validated since this form was written, and
                // never stored: for a guest it is the only way to reach them.
                'email' => $this->userEmail,
                'notes' => $this->notes,
                'staff_id' => $defaultStaffId,
                // Without this a booking made here is invisible to the team
                // availability query that is supposed to stop double-booking.
                'team_id' => $property->team_id,
                'status' => 'confirmed',
                'booking_type' => 'viewing',
            ]);

            $calendarService = app(CalendarIntegrationService::class);
            $this->googleCalendarUrl = $calendarService->getBookingGoogleCalendarUrl($booking);
            $this->outlookCalendarUrl = $calendarService->getBookingOutlookCalendarUrl($booking);
            $this->confirmedBookingId = $booking->id;
            $this->bookingConfirmed = true;

            broadcast(new BookingCreated($booking))->toOthers();

            if (auth()->check()) {
                auth()->user()->notify(new BookingNotification($booking, 'confirmed'));
            }

            // A guest has no account to be notified through, so the address
            // they just gave us is the only route to them.
            if (filled($this->userEmail)) {
                try {
                    Mail::to($this->userEmail)->send(new ViewingBooked($booking->load('property')));
                } catch (\Throwable $e) {
                    // The viewing is booked either way; losing the confirmation
                    // email must not lose the booking with it.
                    Log::error('Could not send the viewing confirmation', [
                        'booking' => $booking->id,
                        'exception' => $e->getMessage(),
                    ]);
                }
            }

            $this->confirmation = __('Booked for :date at :time. The agent will call if anything changes.', [
                'date' => Carbon::parse($this->selectedDate)->format('l j F Y'),
                'time' => $this->selectedTime,
            ]);
            $this->reset(['selectedDate', 'selectedTime', 'userName', 'userEmail', 'userContact', 'notes', 'availableTimeSlots']);
        } catch (Exception $e) {
            Log::error('Booking failed: ' . $e->getMessage());

            $errorMessage = 'Failed to schedule viewing. ';
            if ($e instanceof QueryException) {
                $errorMessage .= 'A database error occurred. ';
            } elseif ($e instanceof ValidationException) {
                $errorMessage .= 'Please check your input and try again. ';
            } elseif ($e->getMessage() === 'Selected date is no longer available.') {
                $errorMessage .= 'The selected date is no longer available. Please choose another date. ';
            } elseif ($e->getMessage() === 'Selected time slot is no longer available.') {
                $errorMessage .= 'The selected time slot is no longer available. Please choose another time. ';
            } else {
                $errorMessage .= 'An unexpected error occurred. ';
            }
            $errorMessage .= 'Please try again or contact support if the problem persists.';

            session()->flash('error', $errorMessage);
        }
    }

    public function render()
    {
        return view('livewire.property-booking', [
            'availableDates' => $this->availableDates,
            'availableTimeSlots' => $this->availableTimeSlots,
        ]);
    }
}
