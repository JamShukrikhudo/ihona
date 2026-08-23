<?php
declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase; use Illuminate\Validation\ValidationException; use Liberu\RealEstate\Instructions\Application\CreateInstruction; use Liberu\RealEstate\Instructions\Application\DeleteInstruction; use Liberu\RealEstate\Instructions\Models\Instruction;
uses(RefreshDatabase::class);
it('creates a draft instruction with checks and terms',function():void{$instruction=app(CreateInstruction::class)->handle(1,5,['subject'=>'Sole agency agreement','ownership_check'=>['verified'=>true],'terms'=>['commission'=>'1.5']]);expect($instruction->status->value)->toBe('draft')->and($instruction->terms['commission'])->toBe('1.5');});
it('rejects empty subjects and archives an instruction for its team',function():void{expect(fn()=>app(CreateInstruction::class)->handle(1,5,['subject'=>'']))->toThrow(ValidationException::class);$instruction=Instruction::query()->create(['team_id'=>1,'subject'=>'Instruction','status'=>'draft']);app(DeleteInstruction::class)->handle($instruction,1);expect(Instruction::withTrashed()->find($instruction->id)->deleted_at)->not->toBeNull();});
