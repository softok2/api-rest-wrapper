<?php

namespace Softok2\RestApiClient\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use ReflectionException;
use Softok2\RestApiClient\Helpers\FinderHelper;
use Softok2\RestApiClient\Services\API\RequestPayload;
use Throwable;

class RestClientService implements RestClientInterface
{
    public Client $client;

    protected string $url;

    protected ?string $secret;

    /**
     * @var callable
     */
    protected $onSuccess = null;

    /**
     * @var callable
     */
    protected $onFailures = null;

    protected int $timeout;

    protected ?string $authToken = null;

    /**
     * @var array<string, string>
     */
    protected array $headers = [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ];

    /**
     * @throws ReflectionException
     */
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

    /**
     * @return array<string, string>
     */
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

    /**
     * @throws ReflectionException
     */
    public function addHeader(string $key, string $value): self
    {
        $this->headers[$key] = $value;

        return new static(
            url: $this->url,
            timeout: $this->timeout,
            secret: $this->secret
        );
    }

    /**
     * @throws ReflectionException
     */
    public function sendRequest(RequestPayload $payload): mixed
    {
        if ($this->authToken) {
            $this->addHeader(
                'Authorization',
                'Bearer '.$this->authToken
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

            $ret = json_decode(
                $response->getBody()->getContents(),
                true
            );

            if ($this->onSuccess) {
                return call_user_func_array($this->onSuccess, [$ret]);
            }

            return $ret;

        } catch (ClientException $e) {
            $ret = json_decode(
                $e->getResponse()->getBody()->getContents(),
                true
            );

            if ($this->onFailures) {
                return call_user_func_array($this->onFailures, [$ret]);
            }

            return $ret;
        } catch (GuzzleException|Exception|Throwable $e) {
            $ret = [config('rest-api-client.default_exception_key') => $e->getMessage()];

            if ($this->onFailures) {
                return call_user_func_array($this->onFailures, [$ret]);
            }

            return $ret;
        }
    }

    /**
     * {@inheritDoc}
     * @throws ReflectionException
     */
    public function post(
        string $path,
        array $body = [],
        string $parametersOption = 'form_params'
    ): mixed {

        $payload = new RequestPayload(
            'POST',
            $path,
            $body,
            $parametersOption
        );

        return $this->sendRequest($payload);
    }

    /**
     * {@inheritDoc}
     * @throws ReflectionException
     */
    public function get(
        string $path,
        array $queryParams = [],
        string $parametersOption = 'query_params'
    ): mixed {
        $payload = new RequestPayload(
            'GET',
            $path,
            $queryParams,
            $parametersOption
        );

        return $this->sendRequest($payload);
    }

    /**
     * {@inheritDoc}
     * @throws ReflectionException
     */
    public function patch(
        string $path,
        array $body = [],
        string $parametersOption = 'form_params'
    ): mixed {
        $payload = new RequestPayload(
            'PATCH',
            $path,
            $body,
            $parametersOption
        );

        return $this->sendRequest($payload);
    }

    /**
     * {@inheritDoc}
     * @throws ReflectionException
     */
    public function delete(
        string $path,
        array $body = [],
        string $parametersOption = 'form_params'
    ): mixed {
        $payload = new RequestPayload(
            'DELETE',
            $path,
            $body,
            $parametersOption
        );

        return $this->sendRequest($payload);
    }

    public function onFailures(callable $callback): self
    {
        $this->onFailures = $callback;

        return $this;
    }

    public function onSuccess(callable $callback): self
    {
        $this->onSuccess = $callback;

        return $this;
    }

    public function withAuth(string $authToken): self
    {
        $this->authToken = $authToken;

        return $this;
    }

    /**
     */
    public function setUrl(string $url): self
    {
        app()->bind(RestClientInterface::class, function () use ($url) {
            return new static(
                url: $url,
                timeout: $this->timeout,
                secret: $this->secret
            );
        });

        return $this;
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
