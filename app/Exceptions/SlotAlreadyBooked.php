<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Someone else holds that hour.
 *
 * Raised rather than a ValidationException because the model has no business
 * knowing what the form calls its field — the reschedule dialog binds
 * `newTime`, the staff panel binds `new_time`, and a message keyed to `time`
 * attached to neither: the customer pressed Reschedule and the form sat there
 * with no change and no reason given.
 */
class SlotAlreadyBooked extends RuntimeException
{
    public static function make(): self
    {
        return new self(__('Someone already has that time. Please choose another.'));
    }
}
