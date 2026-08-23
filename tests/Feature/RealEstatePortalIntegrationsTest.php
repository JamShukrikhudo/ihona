<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\RealEstate\OnTheMarket\Application\CreateOnTheMarketSync;
use Liberu\RealEstate\OnTheMarket\Models\OnTheMarketSync;
use Liberu\RealEstate\Rightmove\Application\CreateRightmoveSync;
use Liberu\RealEstate\Rightmove\Models\RightmoveSync;
use Liberu\RealEstate\Zoopla\Application\CreateZooplaSync;
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
