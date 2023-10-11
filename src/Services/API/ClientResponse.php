<?php

namespace Softok2\RestApiClient\Services\API;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Response;

class ClientResponse
{
    public function __construct(
        protected int $statusCode,
        protected mixed $content,
        protected GuzzleException|\Exception|\Throwable|null $exception = null,
    ) {
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getException(): GuzzleException|\Exception|\Throwable
    {
        return $this->exception;
    }

    public function getContent(): mixed
    {
        return $this->content;
    }

    public function isSuccessful(): bool
    {
        return $this->statusCode >= Response::HTTP_OK && $this->statusCode < Response::HTTP_MULTIPLE_CHOICES;
    }

    public function failed(): bool
    {
        return ! $this->isSuccessful();
    }
}
