<?php

namespace Softok2\RestApiClient\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use ReflectionException;
use Softok2\RestApiClient\Helpers\FinderHelper;
use Softok2\RestApiClient\Services\API\ClientResponse;
use Softok2\RestApiClient\Services\API\RequestPayload;
use Throwable;

class RestClientService implements RestClientInterface
{
    public Client $client;

    protected string $url;

    protected string $authHandler;

    protected ?string $bearerToken = null;

    protected array $basicHttpAuth = [];

    /**
     * @var callable
     */
    protected $onSuccess = null;

    /**
     * @var callable
     */
    protected $onFailures = null;

    protected int $timeout;

    /**
     * @var array<string, string>
     */
    protected array $headers = [
        'Accept' => 'application/json',
    ];

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function __construct(
        string $url,
        int $timeout,
        string $authHandler
    ) {
        $this->url = $url;
        $this->timeout = $timeout;
        $this->authHandler = $authHandler;

        $this->initClient()
            ->setUpAuthHandler()
            ->initResourcesClasses();
    }

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @throws Exception
     */
    protected function initClient(): self
    {
        $this->client = new Client([
            'base_uri' => $this->url,
            'timeout' => $this->timeout,
        ]);

        return $this;
    }

    protected function addHeader(string $key, string $value): void
    {
        $this->headers[$key] = $value;
    }

    public function sendRequest(RequestPayload $payload): mixed
    {
        // Authorize request if needed
        if ($this->bearerToken) {
            $this->addHeader(
                'Authorization',
                'Bearer '.$this->bearerToken
            );
        } elseif (! empty($this->auth)) {
            $options['auth'] = $this->auth;
        }

        // Prepare request headers
        $options['headers'] = $this->getHeaders();

        // Prepare request params
        $options[$payload->getParametersOption()] = $payload->getBody();

        // Send request
        try {
            $response = $this->client->request(
                $payload->getMethod(),
                $payload->getPath(),
                $options
            );

            $clientResponse = new ClientResponse(
                statusCode: $response->getStatusCode(),
                content: json_decode(
                    $response->getBody()->getContents(),
                    true
                ),
            );

            if ($this->onSuccess) {
                return call_user_func_array($this->onSuccess,
                    [$clientResponse]);
            }

            return $clientResponse;

        } catch (ClientException|ServerException|RequestException $e) {
            $content = json_decode(
                $e->getResponse()->getBody()->getContents(),
                true
            );

            $clientResponse = new ClientResponse(
                statusCode: $e->getResponse()->getStatusCode(),
                content: $content,
                exception: $e
            );

            if ($this->onFailures) {
                return call_user_func_array($this->onFailures,
                    [$clientResponse]);
            }

            return $clientResponse;
        } catch (GuzzleException|Exception|Throwable $e) {

            $clientResponse = new ClientResponse(
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
                content: [config('rest-api-client.default_exception_key') => $e->getMessage()],
                exception: $e
            );

            if ($this->onFailures) {
                return call_user_func_array($this->onFailures,
                    [$clientResponse]);
            }

            return $clientResponse;
        }
    }

    /**
     * {@inheritDoc}
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
     */
    public function get(
        string $path,
        array $queryParams = [],
        string $parametersOption = 'query'
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

    public function bearer(?string $bearerToken): self
    {
        $this->bearerToken = $bearerToken;

        return $this;
    }

    public function basicAuth(?string $username, ?string $password): self
    {
        $this->basicHttpAuth = [$username, $password];

        return $this;
    }

    /**
     * Available key options are timeout,authHandler,bearer,auth
     *
     * @return $this
     */
    public function setUrl(string $url, $options = []): self
    {
        $this->bearer($options['bearer'] ?? $this->bearerToken);

        if (Arr::has($options, 'auth')) {
            $this->basicAuth(
                username: $options['auth'][0] ?? null,
                password: $options['auth'][1] ?? null);
        }

        app()->bind(RestClientInterface::class,
            function () use ($url, $options) {
                return new static(
                    url: $url,
                    timeout: $options['timeout'] ?? $this->timeout,
                    authHandler: $options['authHandler'] ?? $this->authHandler
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

    /**
     * @throws Exception
     */
    public function setUpAuthHandler(): self
    {
        if (! in_array($this->authHandler, ['jwt', 'basic', null])) {
            throw new Exception('Invalid auth handler. Only support jwt and basic');
        }

        if ($this->authHandler === 'basic') {
            $this->basicAuth(
                username: config('rest-api-client.auth_handler_options.basic.username'),
                password: config('rest-api-client.auth_handler_options.basic.password')
            );
        } elseif ($this->authHandler === 'jwt') {
            $this->bearer(config('rest-api-client.auth_handler_options.jwt.token'));
        }

        return $this;
    }

    public function getBasicAuth(): array
    {
        return $this->basicHttpAuth;
    }
}
