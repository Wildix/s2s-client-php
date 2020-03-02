<?php

namespace Wildix\Integrations\Requests;

use GuzzleHttp\Psr7\Request as BaseRequest;

class Request extends BaseRequest implements RequestInterface
{

    private $body;

    /**
     * Request constructor.
     *
     * @param string $method
     * @param string $uri
     * @param array $headers
     * @param array|null $body
     * @param string $version
     */
    public function __construct(string $method, string $uri, array $headers = [], array $body = null, string $version = '1.1')
    {
        parent::__construct($method, $uri, $headers, null, $version);
        $this->body = $body;
    }

    /**
     * Gets the body of the message.
     *
     * @return array|null body.
     */
    public function getBody(): ?array
    {
        return $this->body;
    }
}
