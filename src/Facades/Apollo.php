<?php

namespace JeffersonGoncalves\Apollo\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JeffersonGoncalves\Apollo\Apollo
 */
class Apollo extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \JeffersonGoncalves\Apollo\Apollo::class;
    }
}
