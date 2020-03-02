<?php

namespace Wildix\Integrations;

use Psr\Http\Message\ResponseInterface;
use Wildix\Integrations\Exceptions\SdkExceptions;
use Wildix\Integrations\HttpClient\HttpClientInterface;
use Wildix\Integrations\HttpClient\HttpClientsFactory;
use Wildix\Integrations\Requests\RequestFactory;

class Client
{
    /**
     * @var RequestFactory
     */
    private $requestFactory;

    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * Integration constructor.
     *
     * @param array $config
     * @param array $httpClientParams
     *
     * @throws SdkExceptions
     */
    public function __construct(array $config, array $httpClientParams = [])
    {
        $config = array_merge([
            'host' => '',
            'app_id' => '',
            'app_name' => '',
            'secret_key' => '',
        ], $config);

        if (!$config['host']) {
            throw new SdkExceptions(sprintf(SdkExceptions::ERROR_INVALID_PARAM, 'host'));
        }
        if (!$config['app_id']) {
            throw new SdkExceptions(sprintf(SdkExceptions::ERROR_INVALID_PARAM, 'app_id'));
        }
        if (!$config['app_name']) {
            throw new SdkExceptions(sprintf(SdkExceptions::ERROR_INVALID_PARAM, 'app_name'));
        }
        if (!$config['secret_key']) {
            throw new SdkExceptions(sprintf(SdkExceptions::ERROR_INVALID_PARAM, 'secret_key'));
        }

        $host = trim($config['host'], '/');
        $httpClientParams = array_merge($httpClientParams, ['base_uri' => $host]);
        $this->httpClient = HttpClientsFactory::createHttpClient($httpClientParams);
        $this->requestFactory = new RequestFactory($config['app_id'], $config['app_name'], $config['secret_key'], $host);
    }

    /**
     * @param string $endpoint
     * @param array $options
     *
     * @return ResponseInterface
     */
    public function get(string $endpoint, array $options = []): ResponseInterface
    {
        return $this->sendRequest('GET', $endpoint, $options);
    }

    /**
     * @param string $endpoint
     * @param array $options
     *
     * @return ResponseInterface
     */
    public function post(string $endpoint, array $options = []): ResponseInterface
    {
        return $this->sendRequest('POST', $endpoint, $options);
    }

    /**
     * @param string $endpoint
     * @param array $options
     *
     * @return ResponseInterface
     */
    public function put(string $endpoint, array $options = []): ResponseInterface
    {
        return $this->sendRequest('PUT', $endpoint, $options);
    }

    /**
     * @param string $endpoint
     * @param array $options
     *
     * @return ResponseInterface
     */
    public function delete(string $endpoint, array $options = []): ResponseInterface
    {
        return $this->sendRequest('DELETE', $endpoint, $options);
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param array $options
     *
     * @return ResponseInterface
     */
    private function sendRequest(string $method, string $endpoint, array $options = []): ResponseInterface
    {
        $params = isset($options['params']) ? (array)$options['params'] : [];
        $body = isset($options['body']) ? (array)$options['body'] : [];
        $headers = isset($options['headers']) ? (array)$options['headers'] : [];
        $request = $this->requestFactory->createRequest($method, $endpoint, $params, $body, $headers);
        return $this->getHttpClient()->send($request);
    }

    /**
     * @return HttpClientInterface HttpClient
     */
    private function getHttpClient(): HttpClientInterface
    {
        return $this->httpClient;
    }
}
