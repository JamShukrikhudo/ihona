<?php

declare(strict_types=1);

namespace Liberu\RealEstate\CoreFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\RealEstate\CoreFilament\Resources\BranchResource;

final class RealEstateCoreFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'real-estate-core';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([BranchResource::class]);
    }

    public function boot(Panel $panel): void {}
}
