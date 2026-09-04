<?php

declare(strict_types=1);

namespace Liberu\Foundation\DeveloperExperienceFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    protected string $view = 'developer-experience-filament::overview';

    public static function getNavigationLabel(): string
    {
        return __('filament.pages.developer_experience');
    }

    public function getTitle(): string
    {
        return __('filament.pages.developer_experience');
    }
}
