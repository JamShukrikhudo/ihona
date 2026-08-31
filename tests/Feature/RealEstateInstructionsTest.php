<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Instructions\Application\CreateInstruction;
use Liberu\RealEstate\Instructions\Application\DeleteInstruction;
use Liberu\RealEstate\Instructions\Application\TransitionInstruction;
use Liberu\RealEstate\Instructions\Domain\InstructionStatus;
use Liberu\RealEstate\Instructions\Models\Instruction;

uses(RefreshDatabase::class);
it('creates a draft instruction with checks and terms', function (): void {
    $instruction = app(CreateInstruction::class)->handle(1, 5, ['subject' => 'Sole agency agreement', 'ownership_check' => ['verified' => true], 'terms' => ['commission' => '1.5']]);
    expect($instruction->status->value)->toBe('draft')->and($instruction->terms['commission'])->toBe('1.5');
});
it('rejects empty subjects and archives an instruction for its team', function (): void {
    expect(fn () => app(CreateInstruction::class)->handle(1, 5, ['subject' => '']))->toThrow(ValidationException::class);
    $instruction = Instruction::query()->create(['team_id' => 1, 'subject' => 'Instruction', 'status' => 'draft']);
    app(DeleteInstruction::class)->handle($instruction, 1);
    expect(Instruction::withTrashed()->find($instruction->id)->deleted_at)->not->toBeNull();
});

it('requires explicit lifecycle transitions for approval and withdrawal', function (): void {
    $instruction = app(CreateInstruction::class)->handle(1, 5, [
        'subject' => 'Agreement',
        'ownership_check' => ['verified' => true],
        'terms' => ['commission' => '1.5'],
        'disclosures' => ['material_facts' => true],
    ]);

    $transition = app(TransitionInstruction::class);
    $instruction = $transition->handle($instruction, 1, 5, InstructionStatus::PendingApproval);
    $instruction = $transition->handle($instruction, 1, 5, InstructionStatus::Approved);
    $instruction = $transition->handle($instruction, 1, 5, InstructionStatus::Withdrawn);

    expect($instruction->status)->toBe(InstructionStatus::Withdrawn)
        ->and($instruction->approved_at)->not->toBeNull()
        ->and($instruction->withdrawn_at)->not->toBeNull()
        ->and(fn () => $transition->handle($instruction, 1, 5, InstructionStatus::Approved))
        ->toThrow(ValidationException::class);
});
