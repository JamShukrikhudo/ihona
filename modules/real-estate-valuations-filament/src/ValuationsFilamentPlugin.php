<?php
declare(strict_types=1);
namespace Liberu\RealEstate\ValuationsFilament;
use Filament\Contracts\Plugin; use Filament\Panel; use Liberu\RealEstate\ValuationsFilament\Resources\ValuationResource;
final class ValuationsFilamentPlugin implements Plugin { public function getId():string{return 'real-estate-valuations';} public function register(Panel $panel):void{$panel->resources([ValuationResource::class]);} public function boot(Panel $panel):void{} }
