<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MediaAndDocumentsLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class MediaAndDocumentsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'real-estate-media-and-documents-livewire');
        Livewire::component('module-real-estate-media-and-documents::media-document-list', Components\MediaDocumentList::class);
    }
}
