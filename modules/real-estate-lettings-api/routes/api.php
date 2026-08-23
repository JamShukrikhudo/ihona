<?php

use Illuminate\Support\Facades\Route;
use Liberu\RealEstate\LettingsApi\Http\Controllers\LettingController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])->prefix('api/v1/real-estate/lettings')->group(function (): void {
    Route::get('/', [LettingController::class, 'index']);
    Route::post('/', [LettingController::class, 'store']);
    Route::get('/{letting}', [LettingController::class, 'show']);
    Route::patch('/{letting}', [LettingController::class, 'update']);
    Route::patch('/{letting}/details', [LettingController::class, 'updateDetails']);
    Route::post('/{letting}/failure', [LettingController::class, 'recordFailure']);
});
