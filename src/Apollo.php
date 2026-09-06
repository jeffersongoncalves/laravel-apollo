<?php

namespace JeffersonGoncalves\Apollo;

use JeffersonGoncalves\Apollo\Resources\Organizations;
use JeffersonGoncalves\Apollo\Resources\People;

/**
 * Entry point exposing one resource per Apollo.io API v1 group.
 */
class Apollo
{
    protected ApolloClient $client;

    public function __construct(string $apiKey, protected int $defaultPerPage = 25)
    {
        $this->client = new ApolloClient($apiKey);
    }

    public function people(): People
    {
        return new People($this->client, $this->defaultPerPage);
    }

    public function organizations(): Organizations
    {
        return new Organizations($this->client, $this->defaultPerPage);
    }
}
