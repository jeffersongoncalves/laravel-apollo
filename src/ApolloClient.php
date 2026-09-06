<?php

namespace JeffersonGoncalves\Apollo;

use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Apollo\Exceptions\ApolloException;

/**
 * Thin wrapper around Laravel's Http client for the Apollo.io REST API v1.
 *
 * Apollo authenticates via an "api_key" field merged into the JSON body of
 * every request (not a header), so every POST goes through this one method.
 *
 * ponytail: Http::retry() already covers transient-failure retries if ever
 * needed later — no custom retry/backoff layer built here speculatively.
 */
class ApolloClient
{
    public function __construct(
        protected string $apiKey,
    ) {}

    /** @param array<string, mixed> $body */
    public function post(string $path, array $body = []): array
    {
        $body['api_key'] = $this->apiKey;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])
            ->baseUrl('https://api.apollo.io/api/v1')
            ->post($path, $body);

        if ($response->failed()) {
            throw ApolloException::fromResponse($response);
        }

        return (array) ($response->json() ?? []);
    }
}
