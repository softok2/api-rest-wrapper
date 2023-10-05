<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Softok2\RestApiClient\Console\Commands\GenerateApiClientResource;
use Softok2\RestApiClient\Services\RestClientInterface;
use Softok2\RestApiClient\Services\RestClientService;

use function PHPUnit\Framework\assertTrue;

it('publishes the configuration file', function () {
    // Remove the config file if it already exists
    $configPath = config_path('rest-api-client.php');
    if (File::exists($configPath)) {
        unlink($configPath);
    }

    // Call the artisan command to publish the configuration
    Artisan::call('vendor:publish', ['--tag' => 'config']);

    // Assert that the configuration file was published
    $this->assertTrue(File::exists($configPath));
});

it('registers RestClientInterface binding', function () {
    // Resolve the binding from the container
    $restClient = app(RestClientInterface::class);

    // Assert that the binding resolves to an instance of APIClient
    $this->assertInstanceOf(RestClientService::class, $restClient);
});

it('create the data transfer object when called', function (string $class) {
    $this->artisan(
        GenerateApiClientResource::class,
        ['name' => $class],
    )->assertSuccessful();

    assertTrue(
        File::exists(
            path: app_path("Services/API/$class.php"),
        ),
    );
})->with([
    'Auth',
    'Profile',
]);
