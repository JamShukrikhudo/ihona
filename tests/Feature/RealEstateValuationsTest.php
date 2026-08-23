<?php
declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase; use Illuminate\Validation\ValidationException; use Liberu\RealEstate\Valuations\Application\CreateValuation; use Liberu\RealEstate\Valuations\Application\DeleteValuation; use Liberu\RealEstate\Valuations\Models\Valuation;
uses(RefreshDatabase::class);
it('creates a draft valuation with pricing metadata',function():void{$valuation=app(CreateValuation::class)->handle(1,5,['subject'=>'Market appraisal','valued_amount'=>350000,'fee_amount'=>250]);expect($valuation->status->value)->toBe('draft')->and($valuation->valued_amount)->toBe('350000.00');});
it('rejects empty subjects and archives a valuation for its team',function():void{expect(fn()=>app(CreateValuation::class)->handle(1,5,['subject'=>'']))->toThrow(ValidationException::class);$valuation=Valuation::query()->create(['team_id'=>1,'subject'=>'Valuation','status'=>'draft']);app(DeleteValuation::class)->handle($valuation,1);expect(Valuation::withTrashed()->find($valuation->id)->deleted_at)->not->toBeNull();});
