<?php

use App\Support\Currency;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

/**
 * site_currency held a symbol while every listing held an ISO code, so the two
 * could not be compared and a filter chip reading "Under $100" could sit above
 * cards priced in pounds. The setting holds a code now and maps through the
 * same table a listing does.
 */
return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->update('general.site_currency', function ($current) {
            $current = trim((string) $current);

            if ($current === '' || isset(Currency::SYMBOLS[strtoupper($current)])) {
                return $current === '' ? Currency::DEFAULT : strtoupper($current);
            }

            $codes = array_keys(Currency::SYMBOLS, $current, true);

            if ($codes === []) {
                logger()->warning("site_currency was '{$current}', which is neither a code nor a symbol this site knows."
                    .' Falling back to '.Currency::DEFAULT.'; set it in the admin panel.');

                return Currency::DEFAULT;
            }

            // '$' is USD, AUD, CAD and NZD at once, and nothing in the old
            // value said which. The first is a guess, so say so.
            if (count($codes) > 1) {
                logger()->warning("site_currency was '{$current}', which could be ".implode(', ', $codes)
                    .'. Assuming '.$codes[0].'; confirm it in the admin panel.');
            }

            return $codes[0];
        });
    }

    public function down(): void
    {
        $this->migrator->update('general.site_currency', fn ($code) => Currency::symbol($code));
    }
};
