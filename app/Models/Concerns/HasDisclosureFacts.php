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
    /** Statuses that mean the price on the record is a monthly rent. */
    private const RENTAL_STATUSES = ['for rent', 'rented', 'to let', 'let', 'let agreed'];

    public function isRental(): bool
    {
        return in_array(strtolower(trim((string) $this->status)), self::RENTAL_STATUSES, strict: true);
    }

    /**
     * Price per square foot. For a rental this reads monthly, because the price
     * on the record is a monthly rent — quoting it against a sale price per
     * square foot would be out by more than a hundredfold.
     *
     * Kept to two decimals below £10, because a monthly rent per square foot is
     * usually between £1 and £3 and rounding it to whole pounds throws away the
     * only part anyone compares.
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

    /** Formatted for the disclosure strip, without the currency symbol. */
    public function pricePerSquareFootForHumans(): ?string
    {
        $rate = $this->pricePerSquareFoot();

        if ($rate === null) {
            return null;
        }

        return $rate < 10 ? number_format($rate, 2) : number_format($rate);
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

    public function pricePerSquareFootLabel(): string
    {
        return $this->isRental() ? '£/sq ft pcm' : '£/sq ft';
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
        $band = strtoupper(trim((string) (
            data_get($this->epc, 'rating') ?: $this->energy_rating
        )));

        return in_array($band, ['A', 'B', 'C', 'D', 'E', 'F', 'G'], strict: true) ? $band : null;
    }

    public function energyScore(): ?int
    {
        $score = data_get($this->epc, 'score') ?? $this->energy_score;

        return is_numeric($score) ? (int) $score : null;
    }
}
