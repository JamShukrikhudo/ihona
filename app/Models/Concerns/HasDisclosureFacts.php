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

    /**
     * Statuses that mean the property is no longer on offer. Sale state lives
     * here, not in `sold_date`, which is nullable and rarely written — the card
     * gated on that alone and so advertised a sold property as new.
     */
    private const CLOSED_STATUSES = [
        'sold' => 'Sold',
        'sold stc' => 'Sold STC',
        // The canonical status the API writes. It is not `sold_stc`, so
        // normalising underscores to spaces never reached it and a property
        // sold subject to contract kept a live "Book a viewing" button.
        'sstc' => 'Sold STC',
        'exchanged' => 'Exchanged',
        'rented' => 'Let',
        'let' => 'Let',
        'let agreed' => 'Let agreed',
        'under offer' => 'Under offer',
        'withdrawn' => 'Withdrawn',
        'archived' => 'Withdrawn',
    ];

    /**
     * The label for that state, or null while the property is still on offer.
     */
    public function closedStateLabel(): ?string
    {
        return self::CLOSED_STATUSES[$this->normalisedStatus()] ?? ($this->sold_date ? __('Sold') : null);
    }

    public function isClosed(): bool
    {
        return $this->closedStateLabel() !== null;
    }

    private function normalisedStatus(): string
    {
        $status = strtolower(trim((string) $this->status));
        $status = str_replace(['_', '-'], ' ', $status);

        return preg_replace('/\s+/', ' ', $status) ?? $status;
    }

    public function isRental(): bool
    {
        return in_array($this->normalisedStatus(), self::RENTAL_STATUSES, strict: true);
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
        $code = trim((string) $this->currency);

        // One table for both, so a filter chip cannot show a different
        // currency from the cards underneath it.
        return $code !== ''
            ? \App\Support\Currency::symbol($code)
            : app(\App\Settings\GeneralSettings::class)->currencySymbol();
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
        // Trimmed: the trailing space on an unmapped code exists to separate it
        // from a number, and there is no number here — the label read
        // "JPY /sq ft".
        return rtrim($this->currencySymbol()).'/sq ft';
    }

    /**
     * The threshold below which a lease starts costing real money. Under 80
     * years the freeholder can charge marriage value to extend, which is the
     * difference between an inconvenience and a five-figure bill — so it is
     * the number a buyer needs to see before they book a viewing.
     */
    public const SHORT_LEASE_YEARS = 80;

    public function tenureForHumans(): ?string
    {
        $tenure = trim((string) $this->tenure);

        if ($tenure === '') {
            return null;
        }

        $label = match (strtolower(str_replace(['-', ' '], '_', $tenure))) {
            'freehold' => __('Freehold'),
            'leasehold' => __('Leasehold'),
            'share_of_freehold' => __('Share of freehold'),
            'commonhold' => __('Commonhold'),
            default => ucfirst($tenure),
        };

        // Years only where they mean something. A freehold with a lease length
        // left over from a previous record would otherwise print it.
        if (! $this->isLeasehold() || $this->lease_years_remaining === null) {
            return $label;
        }

        return __(':tenure, :count years remaining', [
            'tenure' => $label,
            'count' => number_format((int) $this->lease_years_remaining),
        ]);
    }

    public function isLeasehold(): bool
    {
        return strtolower(trim((string) $this->tenure)) === 'leasehold';
    }

    public function hasShortLease(): bool
    {
        return $this->isLeasehold()
            && $this->lease_years_remaining !== null
            && (int) $this->lease_years_remaining < self::SHORT_LEASE_YEARS;
    }

    /**
     * The certificate's own figure, or the sum of the costs it itemises.
     * Adding up a certificate is arithmetic; producing a bill from a band
     * alone would be invention, so that returns nothing.
     */
    public function annualEnergyCost(): ?float
    {
        $total = data_get($this->epc, 'annual_energy_cost');

        if (is_numeric($total)) {
            return (float) $total;
        }

        $items = array_filter([
            data_get($this->epc, 'heating_cost'),
            data_get($this->epc, 'hot_water_cost'),
            data_get($this->epc, 'lighting_cost'),
        ], 'is_numeric');

        return $items === [] ? null : array_sum(array_map('floatval', $items));
    }

    /**
     * Money the buyer pays every year. Zero is a fact — some leases genuinely
     * carry no ground rent — so it is named rather than left to read as an
     * empty cell.
     */
    public function annualCostForHumans(?string $amount): ?string
    {
        if ($amount === null || $amount === '') {
            return null;
        }

        if ((float) $amount === 0.0) {
            return __('Peppercorn');
        }

        return __(':amount a year', [
            'amount' => $this->currencySymbol().number_format((float) $amount),
        ]);
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
     * The score has to belong to the band being shown, or a certificate's band
     * B ends up wearing the legacy column's band-D 55.
     *
     * The certificate wins. Where it carries a rating but no score, the column
     * is used only if it agrees on the band — that is the same reading, just
     * recorded twice — and dropped when it contradicts.
     */
    public function energyScore(): ?int
    {
        $certificateBand = $this->normaliseBand(data_get($this->epc, 'rating'));

        if ($certificateBand === null) {
            return is_numeric($this->energy_score) ? (int) $this->energy_score : null;
        }

        $score = data_get($this->epc, 'score');

        if (! is_numeric($score) && $this->normaliseBand($this->energy_rating) === $certificateBand) {
            $score = $this->energy_score;
        }

        return is_numeric($score) ? (int) $score : null;
    }

    /** A listing dated in the future is not yet on the market. */
    public function isComingSoon(): bool
    {
        return $this->list_date instanceof CarbonInterface
            && $this->list_date->startOfDay()->greaterThan(now()->startOfDay());
    }

    private function normaliseBand(mixed $band): ?string
    {
        $band = strtoupper(trim((string) $band));

        return in_array($band, ['A', 'B', 'C', 'D', 'E', 'F', 'G'], strict: true) ? $band : null;
    }
}
