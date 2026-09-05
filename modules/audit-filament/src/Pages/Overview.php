<?php

declare(strict_types=1);

namespace Liberu\Foundation\AuditFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    protected string $view = 'audit-filament::overview';

    protected static ?string $slug = 'overview-audit';

    public static function getNavigationLabel(): string
    {
        return __('filament.pages.audit');
    }

    public function getTitle(): string
    {
        return __('filament.pages.audit');
    }
}
