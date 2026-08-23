<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Liberu\RealEstate\OnTheMarket\Application\CreateOnTheMarketSync;
use Liberu\RealEstate\OnTheMarket\Application\SyncOnTheMarketListing;
use Liberu\RealEstate\OnTheMarket\Models\OnTheMarketSync;
use Liberu\RealEstate\Rightmove\Application\CreateRightmoveSync;
use Liberu\RealEstate\Rightmove\Application\SyncRightmoveListing;
use Liberu\RealEstate\Rightmove\Models\RightmoveSync;
use Liberu\RealEstate\Zoopla\Application\CreateZooplaSync;
use Liberu\RealEstate\Zoopla\Application\SyncZooplaListing;
use Liberu\RealEstate\Zoopla\Models\ZooplaSync;

uses(RefreshDatabase::class);

it('keeps Rightmove, Zoopla, and OnTheMarket sync cores independent', function (): void {
    $rightmove = app(CreateRightmoveSync::class)->handle(31, 7, ['listing_id' => 1, 'external_id' => 'rm-1']);
    $zoopla = app(CreateZooplaSync::class)->handle(31, 7, ['listing_id' => 2, 'external_id' => 'zp-2']);
    $onTheMarket = app(CreateOnTheMarketSync::class)->handle(31, 7, ['listing_id' => 3, 'external_id' => 'otm-3']);

    expect($rightmove)->toBeInstanceOf(RightmoveSync::class)->status->value->toBe('pending');
    expect($zoopla)->toBeInstanceOf(ZooplaSync::class)->status->value->toBe('pending');
    expect($onTheMarket)->toBeInstanceOf(OnTheMarketSync::class)->status->value->toBe('pending');
});

it('syncs each provider independently through its transport contract', function (): void {
    Http::fake([
        '*oauth/token' => Http::response(['access_token' => 'test-token']),
        '*' => Http::response(['accepted' => true]),
    ]);

    $rightmove = app(CreateRightmoveSync::class)->handle(31, 7, ['listing_id' => 1]);
    $zoopla = app(CreateZooplaSync::class)->handle(31, 7, ['listing_id' => 2]);
    $onTheMarket = app(CreateOnTheMarketSync::class)->handle(31, 7, ['listing_id' => 3]);

    $rightmove = app(SyncRightmoveListing::class)->handle($rightmove, 'rm-1', ['price' => 100], ['client_id' => 'client', 'client_secret' => 'secret']);
    $zoopla = app(SyncZooplaListing::class)->handle($zoopla, 'zp-2', ['price' => 200], ['certificate' => 'certificate.pem', 'key' => 'key.pem']);
    $onTheMarket = app(SyncOnTheMarketListing::class)->handle($onTheMarket, 'otm-3', ['price' => 300], ['certificate' => 'certificate.pem', 'key' => 'key.pem']);

    expect($rightmove->status->value)->toBe('synced')->and($rightmove->payload['response']['accepted'])->toBeTrue();
    expect($zoopla->status->value)->toBe('synced')->and($zoopla->payload['response']['accepted'])->toBeTrue();
    expect($onTheMarket->status->value)->toBe('synced')->and($onTheMarket->payload['response']['accepted'])->toBeTrue();
    Http::assertSentCount(4);
});
