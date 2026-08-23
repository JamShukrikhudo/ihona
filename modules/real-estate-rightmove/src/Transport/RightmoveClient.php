<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Rightmove\Transport;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Liberu\RealEstate\Rightmove\Contracts\RightmoveTransport;
use RuntimeException;

final class RightmoveClient implements RightmoveTransport
{
    private function request(array $credentials): PendingRequest
    {
        $clientId = (string) ($credentials['client_id'] ?? config('rightmove.client_id'));
        $clientSecret = (string) ($credentials['client_secret'] ?? config('rightmove.client_secret'));
        if ($clientId === '' || $clientSecret === '') {
            throw new RuntimeException('Rightmove client credentials are required.');
        }$token = Http::timeout((int) config('rightmove.timeout', 30))->asForm()->withBasicAuth($clientId, $clientSecret)->post((string) config('rightmove.token_url'));
        $token->throw();
        $accessToken = (string) $token->json('access_token');
        if ($accessToken === '') {
            throw new RuntimeException('Rightmove did not return an access token.');
        }

return Http::timeout((int) config('rightmove.timeout', 30))->baseUrl((string) config('rightmove.base_url'))->withToken($accessToken)->acceptJson();
    }

    public function sendProperty(string $reference, array $payload, array $credentials): array
    {
        $path = str_replace('{reference}', rawurlencode($reference), (string) config('rightmove.send_path'));
        $response = $this->request($credentials)->put($path, $payload);
        $response->throw();

        return $response->json() ?? [];
    }

    public function removeProperty(string $reference, array $credentials): array
    {
        $path = str_replace('{reference}', rawurlencode($reference), (string) config('rightmove.remove_path'));
        $response = $this->request($credentials)->delete($path);
        $response->throw();

        return $response->json() ?? [];
    }

    public function branchPropertyList(array $credentials): array
    {
        $response = $this->request($credentials)->get((string) config('rightmove.branch_list_path'));
        $response->throw();

        return $response->json() ?? [];
    }
}
