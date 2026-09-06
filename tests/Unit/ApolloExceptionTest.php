<?php

use Illuminate\Http\Client\Response;
use Illuminate\Http\Client\Response as HttpResponse;
use JeffersonGoncalves\Apollo\Exceptions\ApolloException;

function fakeApolloResponse(int $status, array $body): Response
{
    $psr = new GuzzleHttp\Psr7\Response($status, [], json_encode($body));

    return new HttpResponse($psr);
}

it('builds the exception message from the response "message" field', function () {
    $response = fakeApolloResponse(404, ['message' => 'Person not found']);

    $exception = ApolloException::fromResponse($response);

    expect($exception->getMessage())->toBe('Person not found')
        ->and($exception->getCode())->toBe(404)
        ->and($exception->errorBody())->toBe(['message' => 'Person not found']);
});

it('falls back to the "error" field when "message" is missing', function () {
    $response = fakeApolloResponse(401, ['error' => 'Invalid API key']);

    $exception = ApolloException::fromResponse($response);

    expect($exception->getMessage())->toBe('Invalid API key');
});

it('falls back to a generic message when the body has no known error keys', function () {
    $response = fakeApolloResponse(500, []);

    $exception = ApolloException::fromResponse($response);

    expect($exception->getMessage())->toBe('Apollo API error (HTTP 500).');
});
