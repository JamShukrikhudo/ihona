<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Zoopla\Transport;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Liberu\RealEstate\Zoopla\Contracts\ZooplaTransport;
use RuntimeException;

final class ZooplaClient implements ZooplaTransport
{
    private function request(array $credentials): PendingRequest
    {
        $certificate = (string) ($credentials['certificate'] ?? config('zoopla.certificate'));
        $key = (string) ($credentials['key'] ?? config('zoopla.key'));
        if ($certificate === '' || $key === '') {
            throw new RuntimeException('Zoopla certificate and private key are required.');
        }$options = ['cert' => $certificate, 'ssl_key' => $key];
        $keyPassword = $credentials['key_password'] ?? config('zoopla.key_password');
        if ($keyPassword !== null) {
            $options['ssl_key'] = [$key, (string) $keyPassword];
        }

        return Http::timeout((int) config('zoopla.timeout', 30))->baseUrl((string) config('zoopla.base_url'))->withOptions($options)->acceptJson();
    }

    public function sendProperty(string $reference, array $payload, array $credentials): array
    {
        $response = $this->request($credentials)->post((string) config('zoopla.send_path'), ['reference' => $reference, 'property' => $payload]);
        $response->throw();

        return $response->json() ?? [];
    }

    public function removeProperty(string $reference, array $credentials): array
    {
        $response = $this->request($credentials)->post((string) config('zoopla.remove_path'), ['reference' => $reference]);
        $response->throw();

        return $response->json() ?? [];
    }

    public function branchPropertyList(array $credentials): array
    {
        $response = $this->request($credentials)->post((string) config('zoopla.branch_list_path'));
        $response->throw();

        return $response->json() ?? [];
    }
}
