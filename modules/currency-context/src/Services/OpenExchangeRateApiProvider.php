<?php

namespace Liberu\Foundation\Currency\Services;

use DateTimeImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Liberu\Foundation\Currency\Contracts\ExchangeRateProvider;
use Liberu\Foundation\Currency\ValueObjects\Currency;
use Liberu\Foundation\Currency\ValueObjects\ExchangeRate;

/**
 * Hits the free, no-key endpoint of exchangerate-api.com (one HTTP request
 * per base currency per `currency.stale_after_seconds`, cached — the
 * upstream data itself only refreshes once a day anyway). Returns null
 * rather than throwing on a network failure or an unlisted currency, so a
 * price display can fall back to showing only the source currency.
 */
final class OpenExchangeRateApiProvider implements ExchangeRateProvider
{
    public function rate(Currency $base, Currency $quote, DateTimeImmutable $effectiveAt): ?ExchangeRate
    {
        $rates = $this->rates($base->code);
        if ($rates === null || ! isset($rates['rates'][$quote->code])) {
            return null;
        }

        return new ExchangeRate(
            base: $base,
            quote: $quote,
            rate: (string) $rates['rates'][$quote->code],
            source: 'open.er-api.com',
            type: 'market',
            effectiveAt: isset($rates['time_last_update_unix'])
                ? (new DateTimeImmutable())->setTimestamp((int) $rates['time_last_update_unix'])
                : $effectiveAt,
            precision: 6,
        );
    }

    /** @return array{rates: array<string, float>, time_last_update_unix?: int}|null */
    private function rates(string $base): ?array
    {
        $ttl = (int) config('currency.stale_after_seconds', 86400);

        return Cache::remember("currency-context:rates:{$base}", $ttl, function () use ($base): ?array {
            $url = str_replace('{base}', $base, (string) config('currency.exchange_rate_api_url'));

            try {
                $response = Http::timeout(5)->get($url);
            } catch (\Throwable) {
                return null;
            }

            if (! $response->successful() || $response->json('result') !== 'success') {
                return null;
            }

            return ['rates' => (array) $response->json('rates'), 'time_last_update_unix' => $response->json('time_last_update_unix')];
        });
    }
}
