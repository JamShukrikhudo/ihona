<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function before(User $user): ?bool
    {
        return $user->hasAnyRole(['staff', 'admin', 'super_admin']) ? true : null;
    }

    public function view(User $user, Appointment $appointment): bool
    {
        return $appointment->user_id === $user->id
            || $appointment->agent_id === $user->id
            || $appointment->staff_id === $user->id
            || ($appointment->team_id !== null && $user->teams()->whereKey($appointment->team_id)->exists());
    }
}
