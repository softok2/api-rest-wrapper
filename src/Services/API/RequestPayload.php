<?php

namespace Softok2\RestApiClient\Services\API;

class RequestPayload
{
    private string $method;

    private string $path;
    private array $body;

    /**
     * @var callable
     */
    private $onError;

    /**
     * @var callable
     */
    private $onSuccess;

    private string $authToken;

    private string $parametersOption;

    /**
     * @param string $path
     * @param array $body
     * @param $onError
     * @param $onSuccess
     * @param string $method
     * @param string $authToken
     * @param string $parametersOption
     */
    public function __construct(
        string $path,
        array $body,
        $onError,
        $onSuccess,
        string $method,
        string $authToken,
        string $parametersOption = 'form_params',
    ) {
        $this->path = $path;
        $this->body = $body;
        $this->onError = $onError;
        $this->onSuccess = $onSuccess;
        $this->authToken = $authToken;
        $this->method = $method;
        $this->parametersOption = $parametersOption;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * @return callable
     */
    public function getOnError(): callable
    {
        return $this->onError;
    }

    /**
     * @return callable
     */
    public function getOnSuccess(): callable
    {
        return $this->onSuccess;
    }

    public function getParametersOption(): bool
    {
        return $this->parametersOption;
    }

    public function getAuthToken(): string
    {
        return $this->authToken;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function hasOnSuccess()
    {
        return !is_null($this->onSuccess);
    }

    public function hasOnError()
    {
        return !is_null($this->onError);
    }


}
