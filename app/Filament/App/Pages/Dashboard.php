<?php

namespace App\Filament\App\Pages;

use App\Filament\App\Widgets\RoleStatsWidget;
use App\Filament\App\Widgets\RoleWelcomeWidget;
use Filament\Pages\Dashboard as BaseDashboard;

final class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Your workspace';

    /** @return array<class-string> */
    public function getWidgets(): array
    {
        return [
            RoleWelcomeWidget::class,
            RoleStatsWidget::class,
        ];
    }

    public function getColumns(): array
    {
        return [
            'default' => 1,
            'lg' => 2,
        ];
    }
}
