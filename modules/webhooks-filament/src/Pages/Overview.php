<?php

declare(strict_types=1);

namespace Liberu\Foundation\WebhooksFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    protected string $view = 'webhooks-filament::overview';

    public static function getNavigationLabel(): string
    {
        return __('filament.pages.webhooks');
    }

    public function getTitle(): string
    {
        return __('filament.pages.webhooks');
    }
}
