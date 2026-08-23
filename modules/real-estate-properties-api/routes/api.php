<?php

use Illuminate\Support\Facades\Route;
use Liberu\RealEstate\PropertiesApi\Http\Controllers\PropertyController;

Route::prefix('api/v1/real-estate/properties')->middleware('auth:sanctum')->group(function (): void {
    Route::get('/', [PropertyController::class, 'index'])->name('real-estate.properties.index');
    Route::post('/', [PropertyController::class, 'store'])->name('real-estate.properties.store');
    Route::get('/{property}', [PropertyController::class, 'show'])->name('real-estate.properties.show');
});
