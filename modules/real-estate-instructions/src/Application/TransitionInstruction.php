<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Instructions\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Instructions\Domain\InstructionStatus;
use Liberu\RealEstate\Instructions\Models\Instruction;

final class TransitionInstruction
{
    public function handle(
        Instruction $instruction,
        int|string $teamId,
        int|string $actorId,
        InstructionStatus $status,
        array $attributes = [],
    ): Instruction {
        abort_unless((string) $instruction->team_id === (string) $teamId, 404);
        if (! $this->canTransition($instruction->status, $status)) {
            throw ValidationException::withMessages(['status' => 'That instruction transition is not allowed.']);
        }

        return DB::transaction(function () use ($instruction, $status, $attributes): Instruction {
            $values = ['status' => $status];
            if ($status === InstructionStatus::Approved) {
                $values['approved_at'] = now();
            }
            if ($status === InstructionStatus::Withdrawn) {
                $values['withdrawn_at'] = now();
            }
            if (array_key_exists('ownership_check', $attributes)) {
                $values['ownership_check'] = $attributes['ownership_check'];
            }
            if (array_key_exists('terms', $attributes)) {
                $values['terms'] = $attributes['terms'];
            }
            if (array_key_exists('disclosures', $attributes)) {
                $values['disclosures'] = $attributes['disclosures'];
            }

            $instruction->forceFill($values)->save();

            return $instruction->fresh();
        });
    }

    private function canTransition(InstructionStatus $from, InstructionStatus $to): bool
    {
        return match ($from) {
            InstructionStatus::Draft => $to === InstructionStatus::PendingApproval,
            InstructionStatus::PendingApproval => in_array($to, [InstructionStatus::Approved, InstructionStatus::Rejected], true),
            InstructionStatus::Approved => $to === InstructionStatus::Withdrawn,
            InstructionStatus::Withdrawn, InstructionStatus::Rejected => false,
        };
    }
}
