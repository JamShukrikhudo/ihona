<?php

declare(strict_types=1);

namespace Liberu\RealEstate\OnTheMarket\Transport;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Liberu\RealEstate\OnTheMarket\Contracts\OnTheMarketTransport;
use RuntimeException;

final class OnTheMarketClient implements OnTheMarketTransport
{
    private function request(array $credentials): PendingRequest
    {
        $certificate = (string) ($credentials['certificate'] ?? config('onthemarket.certificate'));
        $key = (string) ($credentials['key'] ?? config('onthemarket.key'));
        if ($certificate === '' || $key === '') {
            throw new RuntimeException('OnTheMarket certificate and private key are required.');
        }$options = ['cert' => $certificate, 'ssl_key' => $key];
        $keyPassword = $credentials['key_password'] ?? config('onthemarket.key_password');
        if ($keyPassword !== null) {
            $options['ssl_key'] = [$key, (string) $keyPassword];
        }

        return Http::timeout((int) config('onthemarket.timeout', 30))->baseUrl((string) config('onthemarket.base_url'))->withOptions($options)->acceptJson();
    }

    public function sendProperty(string $reference, array $payload, array $credentials): array
    {
        $response = $this->request($credentials)->post((string) config('onthemarket.send_path'), ['reference' => $reference, 'property' => $payload]);
        $response->throw();

        return $response->json() ?? [];
    }

    public function removeProperty(string $reference, array $credentials): array
    {
        $response = $this->request($credentials)->post((string) config('onthemarket.remove_path'), ['reference' => $reference]);
        $response->throw();

        return $response->json() ?? [];
    }

    public function branchPropertyList(array $credentials): array
    {
        $response = $this->request($credentials)->post((string) config('onthemarket.branch_list_path'));
        $response->throw();

        return $response->json() ?? [];
    }
}
