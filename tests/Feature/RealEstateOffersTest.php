<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Offers\Application\CreateOffer;
use Liberu\RealEstate\Offers\Application\DeleteOffer;
use Liberu\RealEstate\Offers\Application\RecordOfferProof;
use Liberu\RealEstate\Offers\Application\TransitionOffer;
use Liberu\RealEstate\Offers\Domain\OfferStatus;
use Liberu\RealEstate\Offers\Models\Offer;

uses(RefreshDatabase::class);
it('creates a draft offer with terms and qualification', function (): void {
    $offer = app(CreateOffer::class)->handle(1, 5, ['subject' => 'Purchase offer', 'amount' => 375000, 'terms' => ['completion' => '30 days'], 'qualification' => ['funds_verified' => true]]);
    expect($offer->status->value)->toBe('draft')->and($offer->amount)->toBe('375000.00');
});
it('requires a valid amount and archives an offer for its team', function (): void {
    expect(fn () => app(CreateOffer::class)->handle(1, 5, ['subject' => 'Offer', 'amount' => -1]))->toThrow(ValidationException::class);
    $offer = Offer::query()->create(['team_id' => 1, 'subject' => 'Offer', 'amount' => 100, 'status' => 'draft']);
    app(DeleteOffer::class)->handle($offer, 1);
    expect(Offer::withTrashed()->find($offer->id)->deleted_at)->not->toBeNull();
});
it('records guarded decisions, proof, and an auditable offer timeline', function (): void {
    $offer = app(CreateOffer::class)->handle(1, 5, ['subject' => 'Purchase offer', 'amount' => 375000, 'property_id' => 42]);
    app(RecordOfferProof::class)->handle($offer, 1, 5, ['mortgage' => 'agreement-in-principle']);
    app(TransitionOffer::class)->handle($offer, 1, 5, OfferStatus::Submitted);
    $offer = app(TransitionOffer::class)->handle($offer->fresh(), 1, 5, OfferStatus::Accepted, ['note' => 'Approved by seller']);
    expect($offer->status)->toBe(OfferStatus::Accepted)->and($offer->proof['mortgage'])->toBe('agreement-in-principle')->and($offer->events()->count())->toBe(4);
});
it('prevents two accepted offers for the same property and team', function (): void {
    $first = app(CreateOffer::class)->handle(1, 5, ['subject' => 'First', 'amount' => 100, 'property_id' => 9]);
    $second = app(CreateOffer::class)->handle(1, 6, ['subject' => 'Second', 'amount' => 110, 'property_id' => 9]);
    $transition = app(TransitionOffer::class);
    $transition->handle($first, 1, 5, OfferStatus::Submitted);
    $transition->handle($first->fresh(), 1, 5, OfferStatus::Accepted);
    $transition->handle($second, 1, 6, OfferStatus::Submitted);
    expect(fn () => $transition->handle($second->fresh(), 1, 6, OfferStatus::Accepted))->toThrow(ValidationException::class);
});
