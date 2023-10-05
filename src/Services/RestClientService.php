<?php

namespace Softok2\RestApiClient\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use ReflectionException;
use Softok2\RestApiClient\Helpers\FinderHelper;
use Softok2\RestApiClient\Services\API\RequestPayload;
use Throwable;

class RestClientService implements RestClientInterface
{
    public Client $client;

    protected string $url;

    protected string $secret;

    protected int $timeout;

    protected array $headers = [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ];

    public function __construct(
        string $url,
        int $timeout,
        string $secret = null
    ) {
        $this->url = $url;
        $this->timeout = $timeout;
        $this->secret = $secret;

        $this->initClient();

        $this->initResourcesClasses();
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    protected function initClient(): void
    {
        $this->client = new Client([
            'base_uri' => $this->url,
            'timeout' => $this->timeout,
            'headers' => $this->getHeaders(),
        ]);
    }

    public function addHeader($key, $value): self
    {
        $this->headers[$key] = $value;

        $this->initClient();

        return $this;
    }

    /**
     * @throws GuzzleException
     * @throws Throwable
     */
    public function sendRequest(RequestPayload $payload)
    {
        if ($payload->getAuthToken()) {
            $this->addHeader(
                'Authorization',
                'Bearer '.$payload->getAuthToken()
            );
        }

        // Prepare request params
        $options[$payload->getParametersOption()] = $payload->getBody();

        // Send request
        try {
            $response = $this->client->request(
                $payload->getMethod(),
                $payload->getPath(),
                $options
            );

            $resp = $response->getBody();

            if ($payload->hasOnSuccess()) {
                return $payload->getOnSuccess()(
                    json_decode($resp->getContents(), true)
                );
            }

            return json_decode($resp->getContents(), true);

        } catch (RequestException|ClientException $e) {
            Log::error($e->getMessage());

            if ($payload->hasOnError()) {
                return $payload->getOnError()($e);
            }

            return $e;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function post(
        string $path,
        $body,
        $token,
        $onError = null,
        $onSuccess = null,
        $parametersOption = 'form_params'
    ) {

        $payload = new RequestPayload(
            $path,
            $body,
            $onError,
            $onSuccess,
            'POST',
            $token
        );

        return $this->sendRequest($payload);
    }

    /**
     * {@inheritDoc}
     */
    public function get(
        string $path,
        $body,
        $token,
        $onError = null,
        $onSuccess = null,
        $parametersOption = 'form_params'
    ) {
        $payload = new RequestPayload(
            $path,
            $body,
            $onError,
            $onSuccess,
            'GET',
            $token,
            $parametersOption
        );

        return $this->sendRequest($payload);
    }

    /**
     * {@inheritDoc}
     */
    public function patch(
        string $path,
        $body,
        $token,
        $onError = null,
        $onSuccess = null,
        $parametersOption = 'form_params'
    ) {
        $payload = new RequestPayload(
            $path,
            $body,
            $onError,
            $onSuccess,
            'PATCH',
            $token,
            $parametersOption
        );

        return $this->sendRequest($payload);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(
        string $path,
        $body,
        $token,
        $onError = null,
        $onSuccess = null,
        $parametersOption = 'form_params'
    ) {
        $payload = new RequestPayload(
            $path,
            $body,
            $onError,
            $onSuccess,
            'DELETE',
            $token,
            $parametersOption
        );

        return $this->sendRequest($payload);
    }

    /**
     * @throws ReflectionException
     */
    public function initResourcesClasses(): void
    {
        $files = FinderHelper::load(config('rest-api-client.resources_path'));

        foreach ($files as $file) {
            $className = pathinfo($file->getFilename(), PATHINFO_FILENAME);

            $class = config('rest-api-client.namespace').$className;

            if (class_exists($class) && method_exists(
                $class,
                'getSlug'
            )) {

                $property = $class::getSlug();

                if (! property_exists($this, $property)) {
                    $this->$property = new $class($this);
                }
            }
        }
    }
}
