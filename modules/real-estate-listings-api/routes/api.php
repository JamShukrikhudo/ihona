<?php

use Illuminate\Support\Facades\Route;
use Liberu\RealEstate\ListingsApi\Http\Controllers\ListingController;

Route::prefix('api/v1/real-estate/listings')->middleware(['api', 'auth:sanctum', 'throttle:api', 'api.idempotency'])->group(function (): void {
    Route::get('/', [ListingController::class, 'index'])->name('real-estate.listings.index');
    Route::post('/', [ListingController::class, 'store'])->name('real-estate.listings.store');
    Route::post('/{listing}/transition/{status}', [ListingController::class, 'transition'])->name('real-estate.listings.transition');
    Route::patch('/{listing}/{section}', [ListingController::class, 'updateSection'])->whereIn('section', ['channel_content', 'publication_rules', 'portal_feeds', 'reconciliation'])->name('real-estate.listings.section');
    Route::get('/{listing}', [ListingController::class, 'show'])->name('real-estate.listings.show');
    Route::match(['put', 'patch'], '/{listing}', [ListingController::class, 'update'])->name('real-estate.listings.update');
    Route::delete('/{listing}', [ListingController::class, 'destroy'])->name('real-estate.listings.destroy');
});
