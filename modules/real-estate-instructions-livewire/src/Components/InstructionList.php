<?php

declare(strict_types=1);

namespace Liberu\RealEstate\InstructionsLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\RealEstate\Instructions\Application\TransitionInstruction;
use Liberu\RealEstate\Instructions\Domain\InstructionStatus;
use Liberu\RealEstate\Instructions\Models\Instruction;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class InstructionList extends Component
{
    #[Validate('nullable|string|max:255')]
    public string $search = '';

    public ?string $error = null;

    public function submitInstruction(int $instructionId, TransitionInstruction $transition): void
    {
        $this->transition($instructionId, InstructionStatus::PendingApproval, $transition);
    }

    public function approveInstruction(int $instructionId, TransitionInstruction $transition): void
    {
        $this->transition($instructionId, InstructionStatus::Approved, $transition);
    }

    public function rejectInstruction(int $instructionId, TransitionInstruction $transition): void
    {
        $this->transition($instructionId, InstructionStatus::Rejected, $transition);
    }

    public function withdrawInstruction(int $instructionId, TransitionInstruction $transition): void
    {
        $this->transition($instructionId, InstructionStatus::Withdrawn, $transition);
    }

    public function render(): View
    {
        $teamId = auth()->user()?->current_team_id;
        $instructions = $teamId === null ? collect() : Instruction::query()->forTeam($teamId)->when($this->search !== '', fn ($query) => $query->where('subject', 'like', '%'.$this->search.'%'))->latest()->paginate(20);

        return view('real-estate-instructions-livewire::instruction-list', ['instructions' => $instructions]);
    }

    private function transition(int $instructionId, InstructionStatus $status, TransitionInstruction $transition): void
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);

        try {
            $transition->handle(
                Instruction::query()->forTeam($user->current_team_id)->findOrFail($instructionId),
                $user->current_team_id,
                $user->getAuthIdentifier(),
                $status,
            );
            $this->error = null;
        } catch (\Throwable $exception) {
            $this->error = $exception->getMessage();
        }
    }
}
