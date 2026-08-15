<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Settings\GeneralSettings;
use App\Support\Currency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Ticket 21 of the Survey Sheet rollout: the site currency disagreed with the
 * listings.
 *
 * A listing carries an ISO code and defaults to GBP; the site-wide setting held
 * a *symbol* and shipped as '$' in its migration while the settings class said
 * '£'. So a filter chip reading "Under $100" could sit directly above cards
 * priced in pounds. Both were behaving as designed and they simply disagreed.
 *
 * The listing's code is authoritative for anything with a listing to read from.
 * The setting is authoritative for everything else — a filter chip has no
 * single listing — and holds a code now, so it maps the same way.
 */
class OneCurrencyOnScreenTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_filter_chip_and_the_cards_below_it_agree(): void
    {
        Property::factory()->count(2)->create(['status' => 'For Sale', 'currency' => 'GBP', 'price' => 450000]);

        $html = $this->get('/properties?maxPrice=500000')->assertOk()->getContent();

        $this->assertStringContainsString('Under £500,000', $html, 'the chip is not in the listings currency');
        $this->assertStringNotContainsString('Under $', $html);
    }

    public function test_the_setting_and_the_listing_default_agree_out_of_the_box(): void
    {
        $settings = app(GeneralSettings::class);
        $property = Property::factory()->create();

        $this->assertSame(
            $property->currencySymbol(),
            $settings->currencySymbol(),
            'a fresh install shows one currency on the cards and another on the chips'
        );
    }

    public function test_changing_the_site_currency_moves_what_has_no_listing_to_read_from(): void
    {
        $settings = app(GeneralSettings::class);
        $settings->site_currency = 'EUR';
        $settings->save();

        Property::factory()->create(['status' => 'For Sale', 'currency' => 'EUR', 'price' => 450000]);

        $this->get('/properties?maxPrice=500000')
            ->assertOk()
            ->assertSee('Under €500,000', false);
    }

    /**
     * The setting holds a code now. Nothing may print it raw, or a page reads
     * "GBP 450,000" where a symbol belongs.
     */
    public function test_no_view_prints_the_setting_raw(): void
    {
        $offenders = [];

        foreach (['resources/views', 'app'] as $directory) {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(base_path($directory)));

            foreach ($files as $file) {
                if ($file->isDir() || ! in_array($file->getExtension(), ['php'], true)) {
                    continue;
                }

                $source = file_get_contents($file->getPathname());

                if (str_contains($source, '->site_currency') && ! str_contains($source, 'Settings/GeneralSettings.php')) {
                    $offenders[] = str_replace(base_path().'/', '', $file->getPathname());
                }
            }
        }

        $offenders = array_values(array_diff($offenders, [
            'app/Settings/GeneralSettings.php',
            'app/Filament/Admin/Pages/ManageGeneralSettings.php',
        ]));

        $this->assertSame([], $offenders, 'these read the raw setting instead of currencySymbol()');
    }

    public function test_a_listing_still_wins_over_the_site_setting(): void
    {
        $settings = app(GeneralSettings::class);
        $settings->site_currency = 'GBP';
        $settings->save();

        $this->assertSame('€', Property::factory()->create(['currency' => 'EUR'])->currencySymbol());
    }

    public function test_both_map_through_the_same_table(): void
    {
        $this->assertSame('£', Currency::symbol('GBP'));
        $this->assertSame('€', Currency::symbol('eur'));
        $this->assertSame('£', Currency::symbol(null), 'an absent code falls back to the pound');

        // Unmapped codes keep the trailing space that separates them from a
        // number; the label trims it.
        $this->assertSame('JPY ', Currency::symbol('JPY'));

        // A database that has not run the migration yet still holds a symbol.
        $this->assertSame('£', Currency::symbol('£'));
        $this->assertSame('$', Currency::symbol('$'));
    }
}
