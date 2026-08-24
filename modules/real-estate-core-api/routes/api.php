<?php

use Illuminate\Support\Facades\Route;
use Liberu\RealEstate\CoreApi\Http\Controllers\AgencyController;
use Liberu\RealEstate\CoreApi\Http\Controllers\BranchController;
use Liberu\RealEstate\CoreApi\Http\Controllers\TerritoryController;

Route::prefix('api/v1/real-estate/branches')->middleware(['api', 'auth:sanctum', 'throttle:api', 'api.idempotency'])->group(function (): void {
    Route::get('/', [BranchController::class, 'index'])->name('real-estate.branches.index');
    Route::post('/', [BranchController::class, 'store'])->name('real-estate.branches.store');
    Route::get('/{branch}', [BranchController::class, 'show'])->name('real-estate.branches.show');
    Route::match(['put', 'patch'], '/{branch}', [BranchController::class, 'update'])->name('real-estate.branches.update');
    Route::delete('/{branch}', [BranchController::class, 'destroy'])->name('real-estate.branches.destroy');
});

Route::prefix('api/v1/real-estate/agencies')->middleware(['api', 'auth:sanctum', 'throttle:api', 'api.idempotency'])->group(function (): void {
    Route::get('/', [AgencyController::class, 'index']);
    Route::post('/', [AgencyController::class, 'store']);
    Route::get('/{agency}', [AgencyController::class, 'show']);
    Route::match(['put', 'patch'], '/{agency}', [AgencyController::class, 'update']);
    Route::delete('/{agency}', [AgencyController::class, 'destroy']);
});

Route::prefix('api/v1/real-estate/territories')->middleware(['api', 'auth:sanctum', 'throttle:api', 'api.idempotency'])->group(function (): void {
    Route::get('/', [TerritoryController::class, 'index']);
    Route::post('/', [TerritoryController::class, 'store']);
    Route::get('/{territory}', [TerritoryController::class, 'show']);
    Route::match(['put', 'patch'], '/{territory}', [TerritoryController::class, 'update']);
    Route::delete('/{territory}', [TerritoryController::class, 'destroy']);
});
