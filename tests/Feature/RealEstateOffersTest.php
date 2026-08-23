<?php
declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase; use Illuminate\Validation\ValidationException; use Liberu\RealEstate\Offers\Application\CreateOffer; use Liberu\RealEstate\Offers\Application\DeleteOffer; use Liberu\RealEstate\Offers\Models\Offer;
uses(RefreshDatabase::class);
it('creates a draft offer with terms and qualification',function():void{$offer=app(CreateOffer::class)->handle(1,5,['subject'=>'Purchase offer','amount'=>375000,'terms'=>['completion'=>'30 days'],'qualification'=>['funds_verified'=>true]]);expect($offer->status->value)->toBe('draft')->and($offer->amount)->toBe('375000.00');});
it('requires a valid amount and archives an offer for its team',function():void{expect(fn()=>app(CreateOffer::class)->handle(1,5,['subject'=>'Offer','amount'=>-1]))->toThrow(ValidationException::class);$offer=Offer::query()->create(['team_id'=>1,'subject'=>'Offer','amount'=>100,'status'=>'draft']);app(DeleteOffer::class)->handle($offer,1);expect(Offer::withTrashed()->find($offer->id)->deleted_at)->not->toBeNull();});
