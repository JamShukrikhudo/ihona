<?php

use Illuminate\Support\Facades\Route;
use Liberu\RealEstate\MediaAndDocumentsApi\Http\Controllers\MediaDocumentController;

Route::prefix('api/v1/real-estate/media-and-documents')->middleware('api')->group(function (): void {
    Route::get('/', [MediaDocumentController::class, 'index'])->name('real-estate.media-documents.index');
    Route::post('/', [MediaDocumentController::class, 'store'])->name('real-estate.media-documents.store');
    Route::get('/{mediaDocument}', [MediaDocumentController::class, 'show'])->name('real-estate.media-documents.show');
    Route::match(['put', 'patch'], '/{mediaDocument}', [MediaDocumentController::class, 'update'])->name('real-estate.media-documents.update');
    Route::delete('/{mediaDocument}', [MediaDocumentController::class, 'destroy'])->name('real-estate.media-documents.destroy');
});
