<?php

namespace Softok2\RestApiClient\Services;

interface RestClientInterface
{
    /**
     * @param  array<string, mixed>  $body
     */
    public function post(
        string $path,
        array $body = [],
        string $parametersOption = 'form_params'
    ): mixed;

    /**
     * @param  array<string, mixed>  $queryParams
     */
    public function get(
        string $path,
        array $queryParams = [],
        string $parametersOption = 'query'
    ): mixed;

    /**
     * @param  array<string, mixed>  $body
     */
    public function patch(
        string $path,
        array $body = [],
        string $parametersOption = 'form_params'
    ): mixed;

    /**
     * @param  array<string, mixed>  $body
     */
    public function put(
        string $path,
        array $body = [],
        string $parametersOption = 'form_params'
    ): mixed;

    /**
     * @param  array<string, mixed>  $body
     */
    public function delete(
        string $path,
        array $body = [],
        string $parametersOption = 'form_params'
    ): mixed;

    public function setUrl(string $url, $options = []): self;

    public function onFailures(callable $callback): self;

    public function onSuccess(callable $callback): self;

    public function bearer(string $bearerToken): self;

    public function initResourcesClasses(): void;
}
