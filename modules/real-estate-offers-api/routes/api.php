<?php

use Illuminate\Support\Facades\Route;
use Liberu\RealEstate\OffersApi\Http\Controllers\OfferController;

Route::prefix('api/v1/real-estate/offers')->middleware(['api', 'auth:sanctum', 'throttle:api'])->group(function (): void {
    Route::get('/', [OfferController::class, 'index'])->name('real-estate.offers.index');
    Route::post('/', [OfferController::class, 'store'])->name('real-estate.offers.store');
    Route::get('/{offer}', [OfferController::class, 'show'])->name('real-estate.offers.show');
    Route::match(['put', 'patch'], '/{offer}', [OfferController::class, 'update'])->name('real-estate.offers.update');
    Route::delete('/{offer}', [OfferController::class, 'destroy'])->name('real-estate.offers.destroy');
});
