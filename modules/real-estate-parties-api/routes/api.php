<?php

use Illuminate\Support\Facades\Route;
use Liberu\RealEstate\PartiesApi\Http\Controllers\PartyController;

Route::prefix('api/v1/real-estate/parties')->middleware(['api', 'auth:sanctum', 'throttle:api', 'api.idempotency'])->group(function (): void {
    Route::get('/', [PartyController::class, 'index'])->name('real-estate.parties.index');
    Route::post('/', [PartyController::class, 'store'])->name('real-estate.parties.store');
    Route::get('/{party}', [PartyController::class, 'show'])->name('real-estate.parties.show');
    Route::match(['put', 'patch'], '/{party}', [PartyController::class, 'update'])->name('real-estate.parties.update');
    Route::post('/{party}/consent', [PartyController::class, 'consent'])->name('real-estate.parties.consent');
    Route::get('/{party}/relationships', [PartyController::class, 'relationships'])->name('real-estate.parties.relationships');
    Route::post('/{party}/relationships', [PartyController::class, 'relationship'])->name('real-estate.parties.relationships.store');
    Route::delete('/{party}/relationships/{relationship}', [PartyController::class, 'destroyRelationship'])->name('real-estate.parties.relationships.destroy');
    Route::delete('/{party}', [PartyController::class, 'destroy'])->name('real-estate.parties.destroy');
});
