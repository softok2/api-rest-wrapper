<?php

namespace Softok2\RestApiClient\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class GenerateApiClientResource extends GeneratorCommand
{
    protected $signature = 'make:apic {name : The Api Client Class Name}';

    protected $description = 'Create a new Api Client Class';

    protected $type = 'Api Client Class';

    protected function getStub(): string
    {
        return __DIR__.'/../../../stubs/api-client.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return "{$rootNamespace}\\Services\\ApiResources";
    }
}
