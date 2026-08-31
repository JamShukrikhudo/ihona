<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Foundation\Settings\Settings\SiteSettings;

uses(RefreshDatabase::class);

it('persists optional agency credentials and discloses them on the public shell', function (): void {
    $settings = app(SiteSettings::class);
    $settings->company_registration_number = '09876543';
    $settings->ico_registration_number = 'ZA123456';
    $settings->vat_number = 'GB 123 4567 89';
    $settings->redress_scheme = 'The Property Ombudsman';
    $settings->save();

    app()->forgetInstance(SiteSettings::class);
    expect(app(SiteSettings::class)->redress_scheme)->toBe('The Property Ombudsman');

    $this->get('/')->assertOk()
        ->assertSee('Company number 09876543')
        ->assertSee('ICO registration ZA123456')
        ->assertSee('VAT number GB 123 4567 89')
        ->assertSee('Redress scheme: The Property Ombudsman');
});

it('omits credentials that are not held', function (): void {
    $this->get('/')->assertOk()
        ->assertDontSee('Company number')
        ->assertDontSee('ICO registration')
        ->assertDontSee('VAT number')
        ->assertDontSee('Redress scheme');
});
