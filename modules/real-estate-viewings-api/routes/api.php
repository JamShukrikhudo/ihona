<?php

use Illuminate\Support\Facades\Route;
use Liberu\RealEstate\ViewingsApi\Http\Controllers\ViewingController;

Route::prefix('api/v1/real-estate/viewings')->middleware(['api', 'auth:sanctum', 'throttle:api'])->group(function (): void {
    Route::get('/', [ViewingController::class, 'index'])->name('real-estate.viewings.index');
    Route::post('/', [ViewingController::class, 'store'])->name('real-estate.viewings.store');
    Route::get('/{viewing}', [ViewingController::class, 'show'])->name('real-estate.viewings.show');
    Route::match(['put', 'patch'], '/{viewing}', [ViewingController::class, 'update'])->name('real-estate.viewings.update');
    Route::delete('/{viewing}', [ViewingController::class, 'destroy'])->name('real-estate.viewings.destroy');
});
