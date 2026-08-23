<?php

use Illuminate\Support\Facades\Route;
use Liberu\RealEstate\PartiesApi\Http\Controllers\PartyController;

Route::prefix('api/v1/real-estate/parties')->middleware('auth:sanctum')->group(function (): void {
    Route::get('/', [PartyController::class, 'index'])->name('real-estate.parties.index');
    Route::post('/', [PartyController::class, 'store'])->name('real-estate.parties.store');
    Route::get('/{party}', [PartyController::class, 'show'])->name('real-estate.parties.show');
    Route::match(['put', 'patch'], '/{party}', [PartyController::class, 'update'])->name('real-estate.parties.update');
    Route::delete('/{party}', [PartyController::class, 'destroy'])->name('real-estate.parties.destroy');
});
