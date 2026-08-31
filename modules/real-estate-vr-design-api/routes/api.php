<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\RealEstate\VrDesignApi\Http\Controllers\VrDesignController;

Route::prefix('api/v1/real-estate/vr-design')->middleware(['api', 'auth:sanctum', 'throttle:api', 'api.idempotency'])->group(function (): void {
    Route::get('/styles', [VrDesignController::class, 'styles'])->name('real-estate.vr-design.styles');
    Route::get('/furniture-categories', [VrDesignController::class, 'furnitureCategories'])->name('real-estate.vr-design.furniture-categories');
    Route::get('/room-types', [VrDesignController::class, 'roomTypes'])->name('real-estate.vr-design.room-types');
    Route::get('/devices', [VrDesignController::class, 'devices'])->name('real-estate.vr-design.devices');
    Route::get('/templates', [VrDesignController::class, 'templates'])->name('real-estate.vr-design.templates');
    Route::get('/properties/{propertyId}/designs', [VrDesignController::class, 'indexForProperty'])->name('real-estate.vr-design.property.index');
    Route::post('/properties/{propertyId}/designs', [VrDesignController::class, 'store'])->name('real-estate.vr-design.property.store');
    Route::get('/designs/{designId}', [VrDesignController::class, 'show'])->name('real-estate.vr-design.show');
    Route::match(['put', 'patch'], '/designs/{designId}', [VrDesignController::class, 'update'])->name('real-estate.vr-design.update');
    Route::delete('/designs/{designId}', [VrDesignController::class, 'destroy'])->name('real-estate.vr-design.destroy');
    Route::post('/designs/{designId}/furniture', [VrDesignController::class, 'addFurniture'])->name('real-estate.vr-design.furniture.add');
    Route::delete('/designs/{designId}/furniture/{furnitureId}', [VrDesignController::class, 'removeFurniture'])->name('real-estate.vr-design.furniture.remove');
    Route::post('/designs/{designId}/clone', [VrDesignController::class, 'cloneDesign'])->name('real-estate.vr-design.clone');
    Route::post('/designs/{designId}/thumbnail', [VrDesignController::class, 'uploadThumbnail'])->name('real-estate.vr-design.thumbnail');
    Route::get('/designs/{designId}/export', [VrDesignController::class, 'export'])->name('real-estate.vr-design.export');
});
