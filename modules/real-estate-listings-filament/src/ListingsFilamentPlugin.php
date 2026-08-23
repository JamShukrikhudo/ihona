<?php
declare(strict_types=1);
namespace Liberu\RealEstate\ListingsFilament;
use Filament\Contracts\Plugin; use Filament\Panel; use Liberu\RealEstate\ListingsFilament\Resources\ListingResource;
final class ListingsFilamentPlugin implements Plugin { public function getId():string{return 'real-estate-listings';} public function register(Panel $panel):void{$panel->resources([ListingResource::class]);} public function boot(Panel $panel):void{} }
