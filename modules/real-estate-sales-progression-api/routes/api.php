<?php

use Illuminate\Support\Facades\Route;
use Liberu\RealEstate\SalesProgressionApi\Http\Controllers\SalesProgressionController;

Route::prefix('api/v1/real-estate/sales-progression')->middleware(['api', 'auth:sanctum', 'throttle:api', 'api.idempotency'])->group(function (): void {
    Route::get('/', [SalesProgressionController::class, 'index']);
    Route::post('/', [SalesProgressionController::class, 'store']);
    Route::get('/{salesProgression}', [SalesProgressionController::class, 'show']);
    Route::match(['put', 'patch'], '/{salesProgression}', [SalesProgressionController::class, 'update']);
    Route::post('/{salesProgression}/transition/{status}', [SalesProgressionController::class, 'transition']);
    Route::patch('/{salesProgression}/{section}', [SalesProgressionController::class, 'updateSection'])->whereIn('section', ['chain', 'milestones', 'professionals', 'completion_controls']);
    Route::delete('/{salesProgression}', [SalesProgressionController::class, 'destroy']);
});
