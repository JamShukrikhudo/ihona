<?php

namespace App\Models\Concerns;

use Carbon\CarbonInterface;

/**
 * The facts behind the disclosure strip.
 *
 * Every one is derived from what the record already holds, never stored, so it
 * cannot drift out of step with the price or the floor area. Each returns null
 * rather than a zero or an empty string when the record does not hold the
 * value — the strip renders "Not supplied" and says the truth instead of
 * implying a property is free, brand new, or listed today.
 */
trait HasDisclosureFacts
{
    /**
     * Statuses that mean the price on the record is a monthly rent.
     *
     * The platform writes these in more than one shape: the API and the staff
     * panel use snake_case ('to_let', 'let_agreed'), while older rows carry the
     * title-case forms the factory still produces ('For Rent'). Separators are
     * normalised before matching, so a new spelling of an existing status
     * cannot quietly turn a rental back into a sale.
     */
    private const RENTAL_STATUSES = ['for rent', 'rented', 'to let', 'let', 'let agreed'];

    public function isRental(): bool
    {
        $status = strtolower(trim((string) $this->status));
        $status = str_replace(['_', '-'], ' ', $status);
        $status = preg_replace('/\s+/', ' ', $status) ?? $status;

        return in_array($status, self::RENTAL_STATUSES, strict: true);
    }

    /**
     * Price per square foot, of whatever the price on the record represents.
     *
     * Kept to two decimals below 10, because a monthly rent per square foot is
     * usually between 1 and 3 and rounding it to whole units throws away the
     * only part anyone compares.
     *
     * The period is not encoded here — see pricePerSquareFootForHumans(), which
     * marks a rental rate as monthly. Without that mark a £12,000 pcm flat at
     * 900 sq ft reads "13" and is indistinguishable from a £13/sq ft sale.
     */
    public function pricePerSquareFoot(): ?float
    {
        $price = (float) ($this->price ?? 0);
        $area = (float) ($this->area_sqft ?? 0);

        if ($price <= 0 || $area <= 0) {
            return null;
        }

        $rate = $price / $area;

        return $rate < 10 ? round($rate, 2) : round($rate);
    }

    /**
     * Formatted for the disclosure strip, without the currency symbol.
     *
     * A rental rate carries "pcm" on the value rather than in the label: the
     * label has to stay identical across cards for the column to align, and
     * "£/sq ft pcm" is wider than the cell.
     */
    public function pricePerSquareFootForHumans(): ?string
    {
        $rate = $this->pricePerSquareFoot();

        if ($rate === null) {
            return null;
        }

        $formatted = $rate < 10 ? number_format($rate, 2) : number_format($rate);

        return $this->isRental() ? $formatted.' '.__('pcm') : $formatted;
    }

    /**
     * The listing's own currency wins over the site-wide symbol: a platform
     * with branches in more than one country stores the code per listing.
     */
    public function currencySymbol(): string
    {
        $symbols = ['GBP' => '£', 'EUR' => '€', 'USD' => '$', 'AUD' => '$', 'CAD' => '$', 'NZD' => '$'];
        $code = strtoupper(trim((string) $this->currency));

        return $symbols[$code]
            ?? ($code ?: (app(\App\Settings\GeneralSettings::class)->site_currency ?: '£'));
    }

    /**
     * Carries the listing's own currency, so a euro-priced property cannot show
     * a euro price above a cell labelled in pounds.
     *
     * The period is deliberately not repeated here: the price line above the
     * strip already reads "£1,150 pcm", and "£/sq ft pcm" is wider than the
     * cell, so it overflowed across the next column's rule.
     */
    public function pricePerSquareFootLabel(): string
    {
        return $this->currencySymbol().'/sq ft';
    }

    /**
     * Days on the market. Stops at the sale so a sold listing does not keep
     * ageing and read as stale stock.
     */
    public function daysListed(): ?int
    {
        $listed = $this->list_date;

        if (! $listed instanceof CarbonInterface) {
            return null;
        }

        $until = $this->sold_date instanceof CarbonInterface ? $this->sold_date : now();

        if ($listed->greaterThan($until)) {
            return null;
        }

        return (int) $listed->startOfDay()->diffInDays($until->startOfDay());
    }

    /**
     * The certificate is the better source, so it wins over the loose column.
     * Anything outside A–G is treated as missing rather than rendered as a
     * band nobody can read.
     */
    public function energyBand(): ?string
    {
        return $this->normaliseBand(data_get($this->epc, 'rating')) ?? $this->normaliseBand($this->energy_rating);
    }

    /**
     * Read from whichever record supplied the band. Choosing a source
     * independently would let a band from the certificate be badged with a
     * score from the legacy column — a band B wearing a band D's 55.
     */
    public function energyScore(): ?int
    {
        $score = $this->normaliseBand(data_get($this->epc, 'rating')) !== null
            ? data_get($this->epc, 'score')
            : $this->energy_score;

        return is_numeric($score) ? (int) $score : null;
    }

    private function normaliseBand(mixed $band): ?string
    {
        $band = strtoupper(trim((string) $band));

        return in_array($band, ['A', 'B', 'C', 'D', 'E', 'F', 'G'], strict: true) ? $band : null;
    }
}
