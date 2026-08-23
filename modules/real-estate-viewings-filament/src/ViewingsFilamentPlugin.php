<?php
declare(strict_types=1);
namespace Liberu\RealEstate\ViewingsFilament;
use Filament\Contracts\Plugin; use Filament\Panel; use Liberu\RealEstate\ViewingsFilament\Resources\ViewingResource;
final class ViewingsFilamentPlugin implements Plugin { public function getId():string{return 'real-estate-viewings';} public function register(Panel $panel):void{$panel->resources([ViewingResource::class]);} public function boot(Panel $panel):void{} }
