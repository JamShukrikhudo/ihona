<?php

declare(strict_types=1);

namespace Liberu\Foundation\AnalyticsMetaFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    protected string $view = 'analytics-meta-filament::overview';

    public static function getNavigationLabel(): string
    {
        return __('filament.pages.analytics_meta');
    }

    public function getTitle(): string
    {
        return __('filament.pages.analytics_meta');
    }
}
