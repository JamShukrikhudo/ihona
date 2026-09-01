<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AdminOverviewWidget;
use Filament\Pages\Dashboard as BaseDashboard;

final class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Operations overview';

    /** @return array<class-string> */
    public function getWidgets(): array
    {
        return [AdminOverviewWidget::class];
    }

    public function getColumns(): int
    {
        return 1;
    }
}
