<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\App;
use Liberu\Foundation\Localization\Http\Middleware\SetLocale;

it('applies a request locale through the registered web middleware group', function () {
    $this->get('/?locale=uz')->assertOk();

    expect(App::getLocale())->toBe('uz')
        ->and(session('locale'))->toBe('uz');
});

it('registers SetLocale on both Filament panels so the authenticated surface localizes', function () {
    expect(Filament::getPanel('admin')->getMiddleware())->toContain(SetLocale::class)
        ->and(Filament::getPanel('app')->getMiddleware())->toContain(SetLocale::class);
});
