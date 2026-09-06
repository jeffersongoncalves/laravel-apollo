<div class="filament-hidden">

![Laravel Apollo](https://raw.githubusercontent.com/jeffersongoncalves/laravel-apollo/main/art/jeffersongoncalves-laravel-apollo.png)

</div>

# Laravel Apollo

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jeffersongoncalves/laravel-apollo.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/laravel-apollo)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/laravel-apollo/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/jeffersongoncalves/laravel-apollo/actions?query=workflow%3ATests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/laravel-apollo/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/jeffersongoncalves/laravel-apollo/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/jeffersongoncalves/laravel-apollo.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/laravel-apollo)
[![License](https://img.shields.io/packagist/l/jeffersongoncalves/laravel-apollo.svg?style=flat-square)](LICENSE.md)

A PHP/Laravel client for the [Apollo.io](https://www.apollo.io/) REST API. Covers people search, organization search, and enrichment (single, bulk, and organization) through a simple, typed API built on Laravel's `Http` client.

## Features

- People: search with title/location/seniority/employee-range/keyword filters, enrich by email/LinkedIn/name+domain, bulk enrich by email
- Organizations: search with location/employee-range/keyword filters, enrich by domain
- Throws `ApolloException` (with the original API error body) on any non-2xx response
- Throws `InvalidArgumentException` before hitting the API when required criteria are missing

## Installation

You can install the package via composer:

```bash
composer require jeffersongoncalves/laravel-apollo
```

Publish the config file:

```bash
php artisan vendor:publish --tag=apollo-config
```

Set your Apollo.io API key in `.env`:

```env
APOLLO_API_KEY=your-api-key
```

Find it under **Settings > Integrations > API** in your Apollo.io account.

## Configuration

```php
// config/apollo.php
return [
    'api_key' => env('APOLLO_API_KEY', ''),
    'default_per_page' => env('APOLLO_DEFAULT_PER_PAGE', 25),
];
```

## Usage

The package is resolved via the `Apollo` facade or by injecting `JeffersonGoncalves\Apollo\Apollo`. Each resource is exposed as a method returning a dedicated resource class.

### People

```php
use JeffersonGoncalves\Apollo\Facades\Apollo;

// Search (supports titles, locations, seniorities, employee_ranges, keywords, page, per_page)
$people = Apollo::people()->search([
    'titles' => 'CEO,CTO',
    'locations' => 'United States,Canada',
    'seniorities' => 'founder,c_suite',
    'employee_ranges' => '1-10,11-50',
    'keywords' => 'fintech',
    'page' => 1,
]);

// Enrich by email, by LinkedIn URL, or by first name + domain
$person = Apollo::people()->enrich(['email' => 'jane@example.com']);
$person = Apollo::people()->enrich(['linkedin' => 'https://linkedin.com/in/janedoe']);
$person = Apollo::people()->enrich(['first_name' => 'Jane', 'domain' => 'example.com']);

// Bulk enrich by email
$matches = Apollo::people()->bulkEnrich(['jane@example.com', 'john@example.com']);
```

### Organizations

```php
$organizations = Apollo::organizations()->search([
    'locations' => 'United States,Canada',
    'employee_ranges' => '1-10,11-50',
    'keywords' => 'fintech',
    'page' => 1,
]);

$organization = Apollo::organizations()->enrich('example.com');
```

### Error handling

Any non-2xx API response throws `JeffersonGoncalves\Apollo\Exceptions\ApolloException`, which exposes the decoded error body:

```php
use JeffersonGoncalves\Apollo\Exceptions\ApolloException;

try {
    Apollo::people()->enrich(['email' => 'unknown@example.com']);
} catch (ApolloException $e) {
    logger()->error($e->getMessage(), $e->errorBody());
}
```

Missing required criteria (e.g. no `email`, `linkedin`, or `first_name` + `domain` on `people()->enrich()`; an empty domain on `organizations()->enrich()`) throw `InvalidArgumentException` before any HTTP call is made.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Jefferson Gonçalves](https://github.com/jeffersongoncalves)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
