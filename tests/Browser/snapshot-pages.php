<?php

/**
 * Re-renders tests/Browser/snapshots/*.html for the browser half of the sweep.
 *
 *   php artisan tinker tests/Browser/snapshot-pages.php
 *   node tests/Browser/public-site-sweep.mjs
 *
 * Asset URLs are rewritten to file:// so the sweep needs no running server.
 * The valuation fixture is written inside a transaction that is rolled back:
 * the sweep must see the estimate block, and the dev database must not grow a
 * row every time someone runs this.
 */

use App\Models\Property;
use App\Models\PropertyValuation;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

$root = 'file://'.public_path();

DB::beginTransaction();

$property = Property::query()->whereNotNull('price')->orderBy('id')->first()
    ?: Property::factory()->create([
        'title' => 'Alexandra Road, Reading RG1',
        'price' => 565000,
        'area_sqft' => 1240,
        'currency' => 'GBP',
        'epc' => ['rating' => 'B', 'score' => 84],
        'latitude' => 51.45,
        'longitude' => -0.97,
    ]);

PropertyValuation::factory()->create([
    'property_id' => $property->id,
    'valuation_type' => 'neural_network',
    'estimated_value' => (float) $property->price * 1.04,
    'confidence_level' => 78,
    'valuation_date' => now()->subDays(4),
    'valid_until' => now()->addMonths(3),
    'status' => 'active',
    'comparable_properties' => ['count' => 9, 'feature_importance' => ['area_sqft' => 41.2, 'bedrooms' => 18.5]],
    'location_factors' => [
        'market_trend' => 'rising',
        'prediction_factors' => ['Large floor area for the postcode', 'Detached property type adds premium'],
    ],
]);

$pages = [
    'home' => '/',
    'properties' => '/properties',
    'search' => '/properties/search',
    'detail' => "/properties/{$property->id}",
    'book' => "/properties/{$property->id}/book",
    'valuation' => "/properties/{$property->id}/valuation",
    'compare' => "/properties/compare/{$property->id}",
    'calculators' => '/calculators',
    'news' => '/news',
    'about' => '/about',
    'services' => '/services',
    'contact' => '/contact',
    'privacy' => '/privacy',
    'terms' => '/terms-and-conditions',
    'design' => '/design',
];

foreach ($pages as $name => $uri) {
    $response = app(Kernel::class)->handle(Request::create($uri, 'GET'));

    if ($response->getStatusCode() !== 200) {
        echo "{$name} ({$uri}) returned {$response->getStatusCode()}\n";

        continue;
    }

    file_put_contents(
        base_path("tests/Browser/snapshots/{$name}.html"),
        str_replace(
            ['"/build/', '"/fonts/', '"/images/'],
            ['"'.$root.'/build/', '"'.$root.'/fonts/', '"'.$root.'/images/'],
            $response->getContent()
        )
    );

    echo "{$name} ok\n";
}

DB::rollBack();
