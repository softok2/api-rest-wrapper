<?php

use Illuminate\Support\Str;
use Softok2\RestApiClient\Helpers\FinderHelper;
use Softok2\RestApiClient\Services\RestClientInterface;
use Softok2\RestApiClient\Services\RestClientService;

use function PHPUnit\Framework\assertTrue;

it('initialize resource classes', function () {

    app(RestClientInterface::class)->initResourcesClasses();

    $files = FinderHelper::load(config('rest-api-client.resources_path'));

    $className = pathinfo($files[0]->getFilename(), PATHINFO_FILENAME);

    $class = config('rest-api-client.namespace').$className;

    $property = $class::getSlug();

    assertTrue(
        is_a(app(RestClientInterface::class)->$property, config('rest-api-client.namespace').'Auth')
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

    $clientInstance = app(RestClientInterface::class)->withAuth($token);

    $clientInstance->get('posts');

    assertTrue($clientInstance->getHeaders()['Authorization'] === 'Bearer '.$token);
});

function forceBind(): void
{
    app()->bind(RestClientInterface::class,
        fn () => new RestClientService(
            'https://jsonplaceholder.typicode.com',
            config('rest-api-client.timeout')
        ));
}
