<?php

namespace JeffersonGoncalves\Apollo\Resources;

use InvalidArgumentException;
use JeffersonGoncalves\Apollo\ApolloClient;

class People
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
            'person_titles' => isset($filters['titles']) ? explode(',', $filters['titles']) : null,
            'person_locations' => isset($filters['locations']) ? explode(',', $filters['locations']) : null,
            'person_seniorities' => isset($filters['seniorities']) ? explode(',', $filters['seniorities']) : null,
            'organization_num_employees_ranges' => isset($filters['employee_ranges'])
                ? array_map('trim', explode(',', $filters['employee_ranges']))
                : null,
            'q_keywords' => $filters['keywords'] ?? null,
        ], fn (mixed $value) => $value !== null);

        return $this->client->post('/mixed_people/search', $body);
    }

    /** @param array<string, mixed> $criteria */
    public function enrich(array $criteria): array
    {
        $body = array_filter([
            'email' => $criteria['email'] ?? null,
            'first_name' => $criteria['first_name'] ?? null,
            'last_name' => $criteria['last_name'] ?? null,
            'domain' => $criteria['domain'] ?? null,
            'linkedin_url' => $criteria['linkedin_url'] ?? $criteria['linkedin'] ?? null,
        ], fn (mixed $value) => $value !== null);

        $hasEmail = isset($body['email']);
        $hasLinkedin = isset($body['linkedin_url']);
        $hasNameAndDomain = isset($body['first_name']) && isset($body['domain']);

        if (! $hasEmail && ! $hasLinkedin && ! $hasNameAndDomain) {
            throw new InvalidArgumentException('Provide "email", "linkedin_url", or both "first_name" and "domain".');
        }

        return $this->client->post('/people/match', $body);
    }

    /** @param array<int, string> $emails */
    public function bulkEnrich(array $emails): array
    {
        if (empty($emails)) {
            throw new InvalidArgumentException('The "emails" array must not be empty.');
        }

        $details = array_map(fn (string $email) => ['email' => trim($email)], $emails);

        return $this->client->post('/people/bulk_match', ['details' => $details]);
    }
}
