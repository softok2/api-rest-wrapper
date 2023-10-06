
![Logo](https://avatars.githubusercontent.com/u/20829145?v=4)


# Softok2 / API Client Wrapper

It's a small wrapper to easily and quickly consume external APIs in Laravel


## Installation

Install API Client Wrapper in your laravel app using 

```bash
composer require softok2/api-client-wrapper
```
    
## Publish configuration file

Publishing the config file is optional:

```bash
php artisan vendor:publish --provider="Softok2\RestApiClient\Providers\ServiceProvider" --tag="config"
```
## Environment Variables

To run this project, you will need to add the following environment variables to your .env file

`REST_API_URL`

`REST_API_TIMEOUT`


## Generate API Resurces

```bash
php artisan make:apic Auth
```
This command would be generate a resource classs under App\Services\API directory. This class look like this:

```php
<?php

class Auth implements ClientResourceInterface
{
    public function __construct(protected RestClientInterface $client)
    {
    }

    /**
     * Retrieve the 'slug' to hook this class into global service client...
     */
    public static function getSlug(): string
    {
        return strtolower('Auth');
    }
}
```

Now you can define a login endpoint in this resources class:

```php
<?php

    public function login(array $data): mixed
    {
        return $this->client->post('/login', $data)
    }
```

You can even add custom callable functions to handle success and error response:


```php
<?php

    public function login(array $data, callable $onSuccess, callable $onError): mixed
    {
        return $this->client->post('/login', $data, $onSuccess, $onError)
    }
```

## Call API Wrapper inside Controller

```php
<?php

class AuthController extends Controller
{
    public function __construct(protected RestClientInterface $client)
    {
    }

  
    public function index()
    {
        
        $data = [
            'user' => request('user'),
            'password' => request('password'),
        ]
        
        try{
         
            $token = $this->client->auth->login($data);

            // Storage token and redirect ...

        }catch(Exception $exception){
           // Handle custom errors 
        }
    }
}
```

## Authors

- [@softok2](https://www.github.com/softok2)


## Running Tests

To run tests, run the following command

```bash
 ./vendor/bin/pest   
```


## Features

- Multiple apis


## License

[MIT](https://choosealicense.com/licenses/mit/)

