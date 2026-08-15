<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Property;
use Carbon\Carbon;
use App\Notifications\BookingNotification;

class VisitBookingService
{
    protected $notificationService;
    protected $calendarService;

    public function __construct(NotificationService $notificationService, CalendarIntegrationService $calendarService)
    {
        $this->notificationService = $notificationService;
        $this->calendarService = $calendarService;
    }

    /**
     * A third copy of the viewing hours lived here, and it disagreed with the
     * public picker: it counted cancelled bookings as taken and offered hours
     * that had already gone. The model owns the hours now.
     */
    public function getAvailableTimeSlots(Property $property, $date)
    {
        return $property->availableViewingSlots((string) $date);
    }

    public function createVisit(array $data)
    {
        $data['visit_type'] = 'property_visit';
        $booking = Booking::create($data);
        $this->scheduleReminder($booking);
        return $booking;
    }

    public function getUpcomingVisits()
    {
        return Booking::visits()->where('date', '>=', now())->orderBy('date')->get();
    }

    public function recordFeedback(Booking $booking, string $feedback)
    {
        $booking->update(['feedback' => $feedback]);
    }

    public function requestFeedback(Booking $booking)
    {
        $feedbackRequestTime = Carbon::parse($booking->date . ' ' . $booking->time)->addHours(2);
        $this->notificationService->scheduleNotification(
            $booking->user,
            new BookingNotification($booking, 'feedback_request'),
            $feedbackRequestTime
        );
    }

    public function scheduleReminder(Booking $booking)
    {
        $reminderTime = Carbon::parse($booking->date . ' ' . $booking->time)->subHours(24);
        $this->notificationService->scheduleNotification(
            $booking->user,
            new BookingNotification($booking, 'reminder'),
            $reminderTime
        );
    }
}