<?php

use JeffersonGoncalves\Apollo\Apollo as ApolloManager;
use JeffersonGoncalves\Apollo\Facades\Apollo;

it('merges the default config', function () {
    expect(config('apollo.default_per_page'))->toBe(25);
});

it('resolves the facade to the manager singleton', function () {
    expect(Apollo::getFacadeRoot())->toBeInstanceOf(ApolloManager::class);
});

it('always resolves the same manager instance', function () {
    expect(app(ApolloManager::class))->toBe(app(ApolloManager::class));
});
