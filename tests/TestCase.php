<?php

namespace JeffersonGoncalves\Apollo\Tests;

use JeffersonGoncalves\Apollo\ApolloServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            ApolloServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('apollo.api_key', 'test-api-key');
    }
}
