<?php

use Illuminate\Support\Facades\Route;
use Liberu\RealEstate\OffersApi\Http\Controllers\OfferController;

Route::prefix('api/v1/real-estate/offers')->middleware(['api', 'auth:sanctum', 'throttle:api', 'api.idempotency'])->group(function (): void {
    Route::get('/', [OfferController::class, 'index'])->name('real-estate.offers.index');
    Route::post('/', [OfferController::class, 'store'])->name('real-estate.offers.store');
    Route::post('/{offer}/transition/{status}', [OfferController::class, 'transition'])->name('real-estate.offers.transition');
    Route::post('/{offer}/proof', [OfferController::class, 'proof'])->name('real-estate.offers.proof');
    Route::get('/{offer}/timeline', [OfferController::class, 'timeline'])->name('real-estate.offers.timeline');
    Route::get('/{offer}', [OfferController::class, 'show'])->name('real-estate.offers.show');
    Route::match(['put', 'patch'], '/{offer}', [OfferController::class, 'update'])->name('real-estate.offers.update');
    Route::delete('/{offer}', [OfferController::class, 'destroy'])->name('real-estate.offers.destroy');
});
