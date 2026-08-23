<?php
declare(strict_types=1);
namespace Liberu\RealEstate\MediaAndDocumentsFilament;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\RealEstate\MediaAndDocumentsFilament\Resources\MediaDocumentResource;
final class MediaAndDocumentsFilamentPlugin implements Plugin { public function getId(): string { return 'real-estate-media-and-documents'; } public function register(Panel $panel): void { $panel->resources([MediaDocumentResource::class]); } public function boot(Panel $panel): void {} }
