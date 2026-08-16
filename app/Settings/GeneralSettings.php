<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{

    public string $site_name = 'Liberu Real Estate';
    public string $site_email = 'info@liberu.co.uk';
    public ?string $site_phone = null;
    public ?string $site_address = null;
    public ?string $site_country = null;
    // An ISO code, not a symbol, so it maps the same way a listing's does.
    // '$' never said which dollar it meant, and the settings migration shipped
    // '$' while this line said '£'.
    public string $site_currency = 'GBP';
    public string $site_default_language = 'en';
    public ?string $facebook_url = null;
    public ?string $twitter_url = null;
    public ?string $github_url = null;
    public ?string $youtube_url = null;
    public string $footer_copyright = '© Liberu Real Estate. All rights reserved.';

    /**
     * Who the visitor is dealing with and what recourse they have. In UK
     * property most of these are a legal requirement rather than a nicety: an
     * agent must publish the redress scheme they belong to, and a limited
     * company must publish its registration number.
     *
     * All optional — a sole trader has no company number, a business under the
     * threshold has no VAT number — and the footer leaves out what is not held
     * rather than printing an empty label.
     */
    public ?string $company_registration_number = null;
    public ?string $ico_registration_number = null;
    public ?string $vat_number = null;
    public ?string $redress_scheme = null;

    /**
     * Everything with no listing to read from — a filter chip, a calculator, a
     * price history — asks for this rather than the raw code.
     */
    public function currencySymbol(): string
    {
        return \App\Support\Currency::symbol($this->site_currency);
    }

    public static function group(): string
    {
        return 'general';
    }
}