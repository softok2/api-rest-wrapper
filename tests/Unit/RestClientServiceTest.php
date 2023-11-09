<?php

use Illuminate\Support\Str;
use Softok2\RestApiClient\Helpers\FinderHelper;
use Softok2\RestApiClient\Services\RestClientInterface;

use function PHPUnit\Framework\assertTrue;

it('initialize resource classes', function () {

    app(RestClientInterface::class)->initResourcesClasses();

    $files = FinderHelper::load(config('rest-api-client.resources_path'));

    $className = pathinfo($files[0]->getFilename(), PATHINFO_FILENAME);

    $class = config('rest-api-client.namespace').$className;

    $property = $class::getSlug();

    assertTrue(
        is_a(app(RestClientInterface::class)->{$property},
            config('rest-api-client.namespace').'Login')
    );
});

it('can process onSuccess hook', function () {
    forceBind();

    $resp = app(RestClientInterface::class)
        ->onSuccess(fn () => 'success')
        ->post(
            '/posts',
            [
                'title' => 'foo',
                'body' => 'bar',
                'userId' => 1,
            ]
        );

    assertTrue($resp === 'success');
});

it('can process onFailures hook', function () {
    forceBind();

    $resp = app(RestClientInterface::class)
        ->onFailures(fn () => 'failure')
        ->delete(
            '/bad-url'
        );

    assertTrue($resp === 'failure');
});

it('load bearer token in header', function () {
    forceBind();

    $token = Str::random();

    $clientInstance = app(RestClientInterface::class)->bearer($token);

    $clientInstance->get('posts/1');

    assertTrue($clientInstance->getHeaders()['Authorization'] === 'Bearer '.$token);
});

it('load basic auth parameters in request options', function () {
    forceBind('basic');

    $clientInstance = app(RestClientInterface::class);

    $clientInstance->get('posts/1');

    assertTrue($clientInstance->getBasicAuth()[0] === 'username');
});

function forceBind($authHandler = null): void
{
    app(RestClientInterface::class)->setUrl(
        url: 'https://jsonplaceholder.typicode.com',
        options: ['authHandler' => $authHandler]
    );
}
