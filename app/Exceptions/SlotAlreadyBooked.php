<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Someone else holds that hour. Not a ValidationException: the model does not
 * know what the form calls its field, and a message keyed to `time` reached no
 * caller.
 */
class SlotAlreadyBooked extends RuntimeException
{
    public static function make(): self
    {
        return new self(__('Someone already has that time. Please choose another.'));
    }
}
