<?php

namespace Wildix\Integrations\HttpClient;

use GuzzleHttp\Client;
use Wildix\Integrations\Requests\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\ClientException;

class GuzzleHttpClient implements HttpClientInterface
{
    /**
     * @var Client The Guzzle.
     */
    private $client;

    /**
     * @param array $params.
     */
    public function __construct(array $params)
    {
        $this->client = new Client($params);
    }

    /**
     * @inheritdoc
     */
    public function send(RequestInterface $request): ResponseInterface
    {
        $contentType = $request->getHeader('Content-Type');
        $isJson = count($contentType) && strpos(strtolower($contentType[0]), 'application/json') !== false;

        try {
            $options = [
                'headers' => $request->getHeaders(),
                ($isJson ? 'json' : 'form_params') => $request->getBody(),
            ];

            $response = $this->client->request($request->getMethod(), $request->getRequestTarget(), $options);
        } catch (ClientException $e) {
            $response = $e->getResponse();
        }
        return $response;
    }
}
