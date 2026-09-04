<?php

declare(strict_types=1);

namespace Liberu\Foundation\ProfilesFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    protected string $view = 'profiles-filament::overview';

    public static function getNavigationLabel(): string
    {
        return __('filament.pages.profiles');
    }

    public function getTitle(): string
    {
        return __('filament.pages.profiles');
    }
}
