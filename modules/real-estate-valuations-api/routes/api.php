<?php

use Illuminate\Support\Facades\Route;
use Liberu\RealEstate\ValuationsApi\Http\Controllers\ValuationController;

Route::prefix('api/v1/real-estate/valuations')->middleware(['api', 'auth:sanctum', 'throttle:api', 'api.idempotency'])->group(function (): void {
    Route::get('/', [ValuationController::class, 'index'])->name('real-estate.valuations.index');
    Route::post('/', [ValuationController::class, 'store'])->name('real-estate.valuations.store');
    Route::get('/{valuation}', [ValuationController::class, 'show'])->name('real-estate.valuations.show');
    Route::match(['put', 'patch'], '/{valuation}', [ValuationController::class, 'update'])->name('real-estate.valuations.update');
    Route::delete('/{valuation}', [ValuationController::class, 'destroy'])->name('real-estate.valuations.destroy');
    Route::post('/{valuation}/schedule', [ValuationController::class, 'schedule'])->name('real-estate.valuations.schedule');
    Route::post('/{valuation}/complete', [ValuationController::class, 'complete'])->name('real-estate.valuations.complete');
    Route::post('/{valuation}/convert', [ValuationController::class, 'convert'])->name('real-estate.valuations.convert');
    Route::post('/{valuation}/comparables', [ValuationController::class, 'comparables'])->name('real-estate.valuations.comparables');
});
