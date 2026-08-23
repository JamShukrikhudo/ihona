<?php
declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase; use Illuminate\Validation\ValidationException; use Liberu\RealEstate\Listings\Application\CreateListing; use Liberu\RealEstate\Listings\Application\DeleteListing; use Liberu\RealEstate\Listings\Models\Listing;
uses(RefreshDatabase::class);
it('creates a draft listing with price and channel content',function():void{$listing=app(CreateListing::class)->handle(1,5,['title'=>'Market listing','price'=>425000,'channel_content'=>['description'=>'A home']]);expect($listing->status->value)->toBe('draft')->and($listing->price)->toBe('425000.00');});
it('rejects empty titles and archives a listing for its team',function():void{expect(fn()=>app(CreateListing::class)->handle(1,5,['title'=>'']))->toThrow(ValidationException::class);$listing=Listing::query()->create(['team_id'=>1,'title'=>'Listing','status'=>'draft']);app(DeleteListing::class)->handle($listing,1);expect(Listing::withTrashed()->find($listing->id)->deleted_at)->not->toBeNull();});
