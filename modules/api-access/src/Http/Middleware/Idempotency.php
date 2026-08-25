<?php

declare(strict_types=1);

namespace Liberu\Foundation\ApiAccess\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Liberu\Foundation\ApiAccess\Support\IdempotencyStore;
use Symfony\Component\HttpFoundation\Response;

final class Idempotency
{
    public function __construct(private readonly IdempotencyStore $store) {}

    public function handle(Request $request, Closure $next): Response
    {
        $key = trim((string) $request->header('Idempotency-Key', ''));
        if ($key === '' || in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true)) {
            return $next($request);
        }

        $identity = sprintf('%s:%s', (string) $request->user()?->getAuthIdentifier(), (string) $request->user()?->current_team_id);
        $existing = $this->store->begin($identity, $key, (string) $request->getContent());
        if ($existing !== null) {
            if ($existing->response_body === null) {
                return response()->json(['message' => 'This request is already in progress.'], 409);
            }

            return response($existing->response_body, (int) $existing->response_status)
                ->header('Content-Type', 'application/json')
                ->header('Idempotency-Replayed', 'true');
        }

        $response = $next($request);
        $this->store->complete($identity, $key, $response->getStatusCode(), $response->getContent());

        return $response->header('Idempotency-Key', $key);
    }
}
