<?php

namespace Softok2\RestApiClient\Services;

use GuzzleHttp\Exception\GuzzleException;
use Throwable;

interface RestClientInterface
{
    /**
     * @throws GuzzleException
     * @throws Throwable
     */
    public function post(
        string $path,
        $body,
        $token,
        $onError = null,
        $onSuccess = null,
        $parametersOption = 'form_params'
    );

    /**
     * @throws GuzzleException
     * @throws Throwable
     */
    public function get(
        string $path,
        $body,
        $token,
        $onError = null,
        $onSuccess = null,
        $parametersOption = 'form_params'
    );

    /**
     * @throws GuzzleException
     * @throws Throwable
     */
    public function patch(
        string $path,
        $body,
        $token,
        $onError = null,
        $onSuccess = null,
        $parametersOption = 'form_params'
    );

    /**
     * @throws GuzzleException
     * @throws Throwable
     */
    public function delete(
        string $path,
        $body,
        $token,
        $onError = null,
        $onSuccess = null,
        $parametersOption = 'form_params'
    );

    public function initResourcesClasses(): void;
}
