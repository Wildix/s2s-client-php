<?php

namespace Wildix\Integrations\HttpClient;

class HttpClientsFactory
{
    /**
     * @param array $params
     *
     * @return HttpClientInterface
     */
    public static function createHttpClient(array $params = []): HttpClientInterface
    {
        return self::detectDefaultClient($params);
    }

    /**
     * Detect default HTTP client.
     *
     * @param array $params Params ;
     *
     * @return HttpClientInterface
     */
    private static function detectDefaultClient(array $params): HttpClientInterface
    {
        return new GuzzleHttpClient($params);
    }
}
