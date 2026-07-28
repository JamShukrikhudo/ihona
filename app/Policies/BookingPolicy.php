<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function before(User $user): ?bool
    {
        return $user->hasAnyRole(['staff', 'admin', 'super_admin']) ? true : null;
    }

    public function view(User $user, Booking $booking): bool
    {
        return $booking->user_id === $user->id
            || ($booking->team_id !== null && $user->teams()->whereKey($booking->team_id)->exists());
    }

    public function update(User $user, Booking $booking): bool
    {
        return $this->view($user, $booking);
    }
}
