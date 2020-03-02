<?php

namespace Wildix\Integrations\Requests;

use Firebase\JWT\JWT;

class RequestFactory
{
    /**
     * 5 minutes
     */
    private const EXPIRE_TIME = 300;

    private const NOT_BODY_METHODS = ['GET', 'DELETE'];

    private $appId;
    private $appName;
    private $secret;
    private $host;
    private $alg;
    private $timeout;

    /**
     * RequestFactory constructor.
     *
     * @param string $appId
     * @param string $appName
     * @param string $secret
     * @param string $host
     * @param int $timeout
     * @param string $alg
     */
    public function __construct(string $appId, string $appName, string $secret, string $host, int $timeout = 3, string $alg = 'sha256')
    {
        $this->appId = trim($appId);
        $this->appName = trim($appName);
        $this->secret = trim($secret);
        $this->host = trim($host);
        $this->alg = trim($alg);
        $this->timeout = $timeout;
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param array $params
     * @param array $body
     * @param array $customHeaders
     *
     * @return RequestInterface
     */
    public function createRequest(string $method, string $endpoint, array $params = [], array $body = [], array $customHeaders = []): RequestInterface
    {
        $query = [];
        parse_str(parse_url($endpoint, PHP_URL_QUERY), $query);
        $endpoint = parse_url('/' . ltrim(trim($endpoint), '/'), PHP_URL_PATH);
        $params = array_merge($query, $params);
        $body = in_array($method, self::NOT_BODY_METHODS) ? [] : $body;
        $headers = array_merge($customHeaders, ['Host' => parse_url($this->host, PHP_URL_HOST)]);
        $headers['X-APP-ID'] = $this->appId;
        $canonicalRequest = $this->generateCanonicalToken($method, $endpoint, $headers, $params, $body);

        if (!empty($params)) {
            $params = http_build_query($params);
            $endpoint .= '?' . $params;
        }

        $headers['Authorization'] = $this->generateAuthToken($headers, $canonicalRequest);
        return new Request($method, $endpoint, $headers, $body);
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param array $headers
     * @param array $params
     * @param array $body
     *
     * @return string
     */
    private function generateCanonicalToken(
        string $method = 'GET',
        string $endpoint = '',
        array $headers = [],
        array $params = [],
        array $body = []
    ): string {
        $canonicalHeaders = $this->getCanonicalData($headers);
        $canonicalParams  = $this->getCanonicalData($params);
        $canonicalBody  = $body ? $this->getCanonicalData($body) : '';

        return $method . $endpoint . $canonicalHeaders . $canonicalParams . $canonicalBody;
    }

    /**
     * Format data to canonical query string.
     *
     * @param array $data Canonical data.
     *
     * @return string
     */
    private function getCanonicalData(array $data): string
    {
        $canonicalData = '';
        $data          = array_change_key_case($data);
        ksort($data);

        foreach ($data as $key => $value) {
            $value = is_bool($value) ? (int)$value : $value;
            $str = (is_array($value)) ? $this->getCanonicalData($value) : trim($value);

            $canonicalData .= $key . ':' . $str . ';';
        }

        return $canonicalData;
    }

    /**
     * @param array $headers
     * @param string $canonicalRequest
     *
     * @return string
     */
    private function generateAuthToken(array $headers, string $canonicalRequest): string
    {
        $time = time();
        $payload = [
            'iss' => $this->appName,
            'iat' => $time,
            'exp' => $time + self::EXPIRE_TIME,
            'sign' => [
                'alg' => $this->alg,
                'headers' => array_keys($headers),
                'hash' => hash($this->alg, $canonicalRequest),
            ],
        ];
        return 'Bearer ' . JWT::encode($payload, $this->secret, 'HS256');
    }
}
