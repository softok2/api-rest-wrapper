<?php

namespace Softok2\RestApiClient\Services\API;

class RequestPayload
{
    private string $method;

    private string $path;

    /**
     * @var array<string, mixed>
     */
    private array $body;

    private string $parametersOption;

    /**
     * @param  array<string, mixed>  $body
     */
    public function __construct(
        string $method,
        string $path,
        array $body,
        string $parametersOption = 'form_params',
    ) {
        $this->path = $path;
        $this->body = $body;
        $this->method = $method;
        $this->parametersOption = $parametersOption;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return array<string, mixed>
     */
    public function getBody(): array
    {
        return $this->body;
    }

    public function getParametersOption(): string
    {
        return $this->parametersOption;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}
