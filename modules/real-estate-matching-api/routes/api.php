<?php

use Illuminate\Support\Facades\Route;
use Liberu\RealEstate\MatchingApi\Http\Controllers\MatchProfileController;

Route::prefix('api/v1/real-estate/matching')->middleware(['api', 'auth:sanctum', 'throttle:api', 'api.idempotency'])->group(function (): void {
    Route::get('/', [MatchProfileController::class, 'index'])->name('real-estate.matching.index');
    Route::post('/', [MatchProfileController::class, 'store'])->name('real-estate.matching.store');
    Route::get('/{matchProfile}', [MatchProfileController::class, 'show'])->name('real-estate.matching.show');
    Route::match(['put', 'patch'], '/{matchProfile}', [MatchProfileController::class, 'update'])->name('real-estate.matching.update');
    Route::delete('/{matchProfile}', [MatchProfileController::class, 'destroy'])->name('real-estate.matching.destroy');
});
