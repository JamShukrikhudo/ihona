<?php

use Illuminate\Support\Facades\Route;
use Liberu\RealEstate\PropertiesApi\Http\Controllers\PropertyController;

Route::prefix('api/v1/real-estate/properties')->middleware(['api', 'auth:sanctum', 'throttle:api', 'api.idempotency'])->group(function (): void {
    Route::get('/', [PropertyController::class, 'index'])->name('real-estate.properties.index');
    Route::post('/', [PropertyController::class, 'store'])->name('real-estate.properties.store');
    Route::post('/{property}/transition/{status}', [PropertyController::class, 'transition'])->name('real-estate.properties.transition');
    Route::put('/{property}/units', [PropertyController::class, 'unit'])->name('real-estate.properties.units');
    Route::post('/{property}/keys', [PropertyController::class, 'key'])->name('real-estate.properties.keys');
    Route::get('/{property}', [PropertyController::class, 'show'])->name('real-estate.properties.show');
    Route::match(['put', 'patch'], '/{property}', [PropertyController::class, 'update'])->name('real-estate.properties.update');
    Route::delete('/{property}', [PropertyController::class, 'destroy'])->name('real-estate.properties.destroy');
});
