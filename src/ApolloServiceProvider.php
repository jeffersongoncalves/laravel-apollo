<?php

namespace JeffersonGoncalves\Apollo;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ApolloServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('apollo')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(Apollo::class, function () {
            return new Apollo(
                (string) config('apollo.api_key'),
                (int) config('apollo.default_per_page', 25),
            );
        });
    }
}
