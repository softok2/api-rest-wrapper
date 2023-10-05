<?php

namespace Softok2\RestApiClient\Tests;

use Orchestra\Testbench\TestCase;
use Softok2\RestApiClient\Providers\ServiceProvider;

class PackageTestCase extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            ServiceProvider::class,
        ];
    }
}
