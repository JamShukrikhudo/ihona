<?php

namespace App\Support;

/**
 * One table, so a filter chip and the cards beneath it cannot disagree.
 *
 * A listing carries an ISO code; the site-wide setting used to carry a symbol,
 * and shipped as '$' in its migration while the settings class said '£'. That
 * put "Under $100" directly above cards priced in pounds. Both now hold a code
 * and both map here.
 */
class Currency
{
    /**
     * @var array<string, string>
     */
    public const SYMBOLS = [
        'GBP' => '£',
        'EUR' => '€',
        'USD' => '$',
        'AUD' => '$',
        'CAD' => '$',
        'NZD' => '$',
        'CHF' => 'CHF ',
        'JPY' => 'JPY ',
    ];

    public const DEFAULT = 'GBP';

    /**
     * A symbol for a code. An unmapped code is returned as the code with a
     * trailing space: printing it flush against the number gave "JPY1,234,000".
     * Anything reading it as a label trims that back off.
     */
    public static function symbol(?string $code): string
    {
        $code = trim((string) $code);

        if ($code === '') {
            return self::SYMBOLS[self::DEFAULT];
        }

        if (isset(self::SYMBOLS[strtoupper($code)])) {
            return self::SYMBOLS[strtoupper($code)];
        }

        // A database that has not run the migration yet still holds a symbol
        // rather than a code, and rendering '£' as '£ ' would be worse than
        // the bug this replaced.
        if (in_array($code, self::SYMBOLS, true)) {
            return $code;
        }

        return strtoupper($code).' ';
    }

    /**
     * Codes offered in the settings panel, labelled so an administrator picks a
     * currency rather than guessing at a symbol — '$' alone never said which
     * dollar it meant.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            'GBP' => 'GBP — Pound sterling (£)',
            'EUR' => 'EUR — Euro (€)',
            'USD' => 'USD — US dollar ($)',
            'AUD' => 'AUD — Australian dollar ($)',
            'CAD' => 'CAD — Canadian dollar ($)',
            'NZD' => 'NZD — New Zealand dollar ($)',
            'CHF' => 'CHF — Swiss franc',
            'JPY' => 'JPY — Japanese yen',
        ];
    }
}
