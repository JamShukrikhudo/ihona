<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use App\Services\CalendarIntegrationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function store(Request $request)
    {

        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'time' => 'required|date_format:H:i',
                'staff_id' => ['required', Rule::exists('users', 'id')],
                'notes' => 'nullable|string|max:2000',
                'contact' => 'nullable|string|max:255',
            ]);

            $this->ensureEligibleStaff($validated['staff_id']);
            $validated['user_id'] = $request->user()?->id;
            $booking = Booking::create($validated);

            return response()->json(['message' => 'Booking created successfully', 'booking' => $booking], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

    }

    public function update(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        $validated = $request->validate([
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'staff_id' => ['required', Rule::exists('users', 'id')],
            'notes' => 'nullable|string|max:2000',
            'contact' => 'nullable|string|max:255',
        ]);

        $this->ensureEligibleStaff($validated['staff_id']);

        // A unique index holds the slot, so moving a booking onto one already
        // taken is a rejected request rather than a 500 with raw SQL in it.
        try {
            $booking->update($validated);
        } catch (\Illuminate\Database\QueryException $e) {
            if (! str_contains($e->getMessage(), 'bookings_property_slot_unique')
                && ! str_contains($e->getMessage(), 'slot_key')) {
                throw $e;
            }

            throw \Illuminate\Validation\ValidationException::withMessages([
                'time' => __('That time is already booked for this property.'),
            ]);
        }

        return response()->json(['message' => 'Booking updated successfully', 'booking' => $booking], 200);
    }

    public function index(Request $request)
    {
        $bookings = Booking::query()
            ->when(
                ! $request->user()->hasAnyRole(['staff', 'admin', 'super_admin']),
                fn ($query) => $query->where('user_id', $request->user()->id)
                    ->orWhereIn('team_id', $request->user()->teams()->select('teams.id'))
            )
            ->paginate(50);

        return response()->json(['bookings' => $bookings], 200);
    }

    public function downloadIcs(Booking $booking, CalendarIntegrationService $calendarService)
    {
        $this->authorize('view', $booking);

        $icsContent = $calendarService->generateBookingIcs($booking);

        return response($icsContent, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="property-viewing.ics"',
        ]);
    }

    private function ensureEligibleStaff(int $staffId): void
    {
        if (! User::findOrFail($staffId)->hasAnyRole(['staff', 'agent', 'admin', 'super_admin'])) {
            throw ValidationException::withMessages([
                'staff_id' => 'The selected user is not eligible to receive bookings.',
            ]);
        }
    }
}
