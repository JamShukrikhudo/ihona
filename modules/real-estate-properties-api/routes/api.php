<?php

use Illuminate\Support\Facades\Route;
use Liberu\RealEstate\PropertiesApi\Http\Controllers\PropertyController;
use Liberu\RealEstate\PropertiesApi\Http\Controllers\PropertyCategoryController;
use Liberu\RealEstate\PropertiesApi\Http\Controllers\PropertyTemplateController;

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

Route::prefix('api/v1/real-estate/property-categories')->middleware(['api', 'auth:sanctum', 'throttle:api', 'api.idempotency'])->group(function (): void {
    Route::get('/', [PropertyCategoryController::class, 'index'])->name('real-estate.property-categories.index');
    Route::post('/', [PropertyCategoryController::class, 'store'])->name('real-estate.property-categories.store');
    Route::match(['put', 'patch'], '/{category}', [PropertyCategoryController::class, 'update'])->name('real-estate.property-categories.update');
    Route::delete('/{category}', [PropertyCategoryController::class, 'destroy'])->name('real-estate.property-categories.destroy');
});

Route::prefix('api/v1/real-estate/property-templates')->middleware(['api', 'auth:sanctum', 'throttle:api', 'api.idempotency'])->group(function (): void {
    Route::get('/', [PropertyTemplateController::class, 'index'])->name('real-estate.property-templates.index');
    Route::post('/', [PropertyTemplateController::class, 'store'])->name('real-estate.property-templates.store');
    Route::match(['put', 'patch'], '/{template}', [PropertyTemplateController::class, 'update'])->name('real-estate.property-templates.update');
    Route::delete('/{template}', [PropertyTemplateController::class, 'destroy'])->name('real-estate.property-templates.destroy');
});
