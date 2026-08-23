<?php
declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase; use Illuminate\Validation\ValidationException; use Liberu\RealEstate\Viewings\Application\CreateViewing; use Liberu\RealEstate\Viewings\Application\DeleteViewing; use Liberu\RealEstate\Viewings\Models\Viewing;
uses(RefreshDatabase::class);
it('creates a requested viewing with access metadata',function():void{$viewing=app(CreateViewing::class)->handle(1,5,['subject'=>'Property viewing','starts_at'=>'2026-09-01 10:00','access'=>['key_holder'=>'agent']]);expect($viewing->status->value)->toBe('requested')->and($viewing->access['key_holder'])->toBe('agent');});
it('requires a start time and archives a viewing for its team',function():void{expect(fn()=>app(CreateViewing::class)->handle(1,5,['subject'=>'Viewing']))->toThrow(ValidationException::class);$viewing=Viewing::query()->create(['team_id'=>1,'subject'=>'Viewing','status'=>'requested','starts_at'=>'2026-09-01 10:00']);app(DeleteViewing::class)->handle($viewing,1);expect(Viewing::withTrashed()->find($viewing->id)->deleted_at)->not->toBeNull();});
