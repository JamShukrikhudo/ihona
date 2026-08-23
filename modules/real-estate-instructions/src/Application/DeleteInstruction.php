<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Instructions\Application;

use Liberu\RealEstate\Instructions\Models\Instruction;

final class DeleteInstruction
{
    public function handle(Instruction $instruction, int|string $teamId): void
    {
        abort_unless((string) $instruction->team_id === (string) $teamId, 404);
        $instruction->delete();
    }
}
