<?php

use Illuminate\Support\Facades\Route;
use Liberu\RealEstate\CoreApi\Http\Controllers\BranchController;

Route::prefix('api/v1/real-estate/branches')->middleware('auth:sanctum')->group(function (): void {
    Route::get('/', [BranchController::class, 'index'])->name('real-estate.branches.index');
    Route::post('/', [BranchController::class, 'store'])->name('real-estate.branches.store');
    Route::get('/{branch}', [BranchController::class, 'show'])->name('real-estate.branches.show');
    Route::match(['put', 'patch'], '/{branch}', [BranchController::class, 'update'])->name('real-estate.branches.update');
    Route::delete('/{branch}', [BranchController::class, 'destroy'])->name('real-estate.branches.destroy');
});
