<?php

use App\Services\API\Auth;
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
        is_a(app(RestClientInterface::class)->$property, Auth::class)
    );
});
