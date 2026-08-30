<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Instructions\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Instructions\Models\Instruction;

final class UpdateInstruction
{
    public function handle(Instruction $instruction, int|string $teamId, array $attributes): Instruction
    {
        abort_unless((string) $instruction->team_id === (string) $teamId, 404);
        if (array_key_exists('subject', $attributes) && trim((string) $attributes['subject']) === '') {
            throw ValidationException::withMessages(['subject' => 'An instruction subject is required.']);
        }
        if (array_key_exists('status', $attributes) || array_key_exists('approved_at', $attributes) || array_key_exists('withdrawn_at', $attributes)) {
            throw ValidationException::withMessages(['status' => 'Instruction lifecycle changes must use the transition action.']);
        }

        $instruction->fill($attributes);
        $instruction->save();

        return $instruction->fresh();
    }
}
