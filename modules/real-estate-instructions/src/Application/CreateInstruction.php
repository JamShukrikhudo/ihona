<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Instructions\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Instructions\Domain\InstructionStatus;
use Liberu\RealEstate\Instructions\Models\Instruction;

final class CreateInstruction
{
    public function handle(int|string $teamId, int|string $actorId, array $attributes): Instruction
    {
        $subject = trim((string) ($attributes['subject'] ?? ''));
        if ($subject === '') {
            throw ValidationException::withMessages(['subject' => 'An instruction subject is required.']);
        }

return DB::transaction(fn (): Instruction => Instruction::query()->create(['team_id' => $teamId, 'created_by' => $actorId, 'property_id' => $attributes['property_id'] ?? null, 'party_id' => $attributes['party_id'] ?? null, 'subject' => $subject, 'status' => InstructionStatus::Draft, 'ownership_check' => $attributes['ownership_check'] ?? [], 'terms' => $attributes['terms'] ?? [], 'disclosures' => $attributes['disclosures'] ?? []]));
    }
}
