<?php

use Illuminate\Support\Facades\Route;
use Liberu\RealEstate\InstructionsApi\Http\Controllers\InstructionController;

Route::prefix('api/v1/real-estate/instructions')->middleware(['api', 'auth:sanctum', 'throttle:api', 'api.idempotency'])->group(function (): void {
    Route::get('/', [InstructionController::class, 'index'])->name('real-estate.instructions.index');
    Route::post('/', [InstructionController::class, 'store'])->name('real-estate.instructions.store');
    Route::post('/{instruction}/transition/{status}', [InstructionController::class, 'transition'])->name('real-estate.instructions.transition');
    Route::get('/{instruction}', [InstructionController::class, 'show'])->name('real-estate.instructions.show');
    Route::match(['put', 'patch'], '/{instruction}', [InstructionController::class, 'update'])->name('real-estate.instructions.update');
    Route::delete('/{instruction}', [InstructionController::class, 'destroy'])->name('real-estate.instructions.destroy');
});
