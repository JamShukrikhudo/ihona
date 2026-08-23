<?php
declare(strict_types=1);
namespace Liberu\RealEstate\InstructionsFilament;
use Filament\Contracts\Plugin; use Filament\Panel; use Liberu\RealEstate\InstructionsFilament\Resources\InstructionResource;
final class InstructionsFilamentPlugin implements Plugin { public function getId():string{return 'real-estate-instructions';} public function register(Panel $panel):void{$panel->resources([InstructionResource::class]);} public function boot(Panel $panel):void{} }
