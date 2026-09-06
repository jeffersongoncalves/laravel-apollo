<?php

use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Apollo\Exceptions\ApolloException;
use JeffersonGoncalves\Apollo\Facades\Apollo;

it('searches people with translated filters', function () {
    Http::fake([
        '*/mixed_people/search' => Http::response(['people' => [['id' => 1, 'name' => 'Jane Doe']]]),
    ]);

    $result = Apollo::people()->search([
        'titles' => 'CEO,CTO',
        'locations' => 'United States,Canada',
        'seniorities' => 'founder,c_suite',
        'employee_ranges' => '1-10, 11-20',
        'keywords' => 'fintech',
        'page' => 2,
    ]);

    expect($result['people'][0]['name'])->toBe('Jane Doe');

    Http::assertSent(function ($request) {
        return str_contains((string) $request->url(), '/mixed_people/search')
            && $request['page'] === 2
            && $request['per_page'] === 25
            && $request['person_titles'] === ['CEO', 'CTO']
            && $request['person_locations'] === ['United States', 'Canada']
            && $request['person_seniorities'] === ['founder', 'c_suite']
            && $request['organization_num_employees_ranges'] === ['1-10', '11-20']
            && $request['q_keywords'] === 'fintech'
            && $request['api_key'] === 'test-api-key';
    });
});

it('searches people with only the default pagination', function () {
    Http::fake(['*/mixed_people/search' => Http::response(['people' => []])]);

    Apollo::people()->search();

    Http::assertSent(fn ($request) => $request['page'] === 1 && $request['per_page'] === 25);
});

it('enriches a person by email', function () {
    Http::fake(['*/people/match' => Http::response(['person' => ['id' => 1, 'email' => 'jane@example.com']])]);

    $result = Apollo::people()->enrich(['email' => 'jane@example.com']);

    expect($result['person']['email'])->toBe('jane@example.com');
    Http::assertSent(fn ($request) => $request['email'] === 'jane@example.com' && $request['api_key'] === 'test-api-key');
});

it('enriches a person by first name and domain', function () {
    Http::fake(['*/people/match' => Http::response(['person' => ['id' => 1]])]);

    Apollo::people()->enrich(['first_name' => 'Jane', 'domain' => 'example.com']);

    Http::assertSent(fn ($request) => $request['first_name'] === 'Jane' && $request['domain'] === 'example.com');
});

it('enriches a person by linkedin shorthand', function () {
    Http::fake(['*/people/match' => Http::response(['person' => ['id' => 1]])]);

    Apollo::people()->enrich(['linkedin' => 'https://linkedin.com/in/janedoe']);

    Http::assertSent(fn ($request) => $request['linkedin_url'] === 'https://linkedin.com/in/janedoe');
});

it('requires email, linkedin_url, or first_name plus domain to enrich a person', function () {
    Apollo::people()->enrich(['first_name' => 'Jane']);
})->throws(InvalidArgumentException::class);

it('bulk enriches people by email', function () {
    Http::fake(['*/people/bulk_match' => Http::response(['matches' => []])]);

    Apollo::people()->bulkEnrich(['jane@example.com', ' john@example.com ']);

    Http::assertSent(fn ($request) => $request['details'] === [
        ['email' => 'jane@example.com'],
        ['email' => 'john@example.com'],
    ]);
});

it('requires at least one email to bulk enrich', function () {
    Apollo::people()->bulkEnrich([]);
})->throws(InvalidArgumentException::class);

it('throws an ApolloException on a failed enrich request', function () {
    Http::fake(['*/people/match' => Http::response(['message' => 'Person not found'], 404)]);

    Apollo::people()->enrich(['email' => 'missing@example.com']);
})->throws(ApolloException::class, 'Person not found');
