<?php

use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Apollo\Exceptions\ApolloException;
use JeffersonGoncalves\Apollo\Facades\Apollo;

it('searches organizations with translated filters', function () {
    Http::fake(['*/mixed_companies/search' => Http::response(['organizations' => [['id' => 1, 'name' => 'Acme Inc']]])]);

    $result = Apollo::organizations()->search([
        'locations' => 'United States,Canada',
        'employee_ranges' => '1-10, 11-20',
        'keywords' => 'fintech',
        'page' => 2,
    ]);

    expect($result['organizations'][0]['name'])->toBe('Acme Inc');

    Http::assertSent(function ($request) {
        return str_contains((string) $request->url(), '/mixed_companies/search')
            && $request['page'] === 2
            && $request['per_page'] === 25
            && $request['organization_locations'] === ['United States', 'Canada']
            && $request['organization_num_employees_ranges'] === ['1-10', '11-20']
            && $request['q_keywords'] === 'fintech'
            && $request['api_key'] === 'test-api-key';
    });
});

it('enriches an organization by domain', function () {
    Http::fake(['*/organizations/enrich' => Http::response(['organization' => ['id' => 1, 'name' => 'Acme Inc']])]);

    $result = Apollo::organizations()->enrich('example.com');

    expect($result['organization']['name'])->toBe('Acme Inc');
    Http::assertSent(fn ($request) => $request['domain'] === 'example.com' && $request['api_key'] === 'test-api-key');
});

it('requires a domain to enrich an organization', function () {
    Apollo::organizations()->enrich('');
})->throws(InvalidArgumentException::class);

it('throws an ApolloException on a failed enrich request', function () {
    Http::fake(['*/organizations/enrich' => Http::response(['message' => 'Invalid domain'], 422)]);

    Apollo::organizations()->enrich('bad-domain');
})->throws(ApolloException::class, 'Invalid domain');
