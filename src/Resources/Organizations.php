<?php

namespace JeffersonGoncalves\Apollo\Resources;

use InvalidArgumentException;
use JeffersonGoncalves\Apollo\ApolloClient;

class Organizations
{
    public function __construct(
        protected ApolloClient $client,
        protected int $defaultPerPage = 25,
    ) {}

    /** @param array<string, mixed> $filters */
    public function search(array $filters = []): array
    {
        $body = array_filter([
            'page' => $filters['page'] ?? 1,
            'per_page' => $filters['per_page'] ?? $this->defaultPerPage,
            'organization_locations' => isset($filters['locations']) ? explode(',', $filters['locations']) : null,
            'organization_num_employees_ranges' => isset($filters['employee_ranges'])
                ? array_map('trim', explode(',', $filters['employee_ranges']))
                : null,
            'q_keywords' => $filters['keywords'] ?? null,
        ], fn (mixed $value) => $value !== null);

        return $this->client->post('/mixed_companies/search', $body);
    }

    public function enrich(string $domain): array
    {
        if ($domain === '') {
            throw new InvalidArgumentException('The "domain" argument must not be empty.');
        }

        return $this->client->post('/organizations/enrich', ['domain' => $domain]);
    }
}
