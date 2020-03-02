<?php

namespace Wildix\Integrations\HttpClient;

use Wildix\Integrations\Requests\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface HttpClientInterface
{
    public function send(RequestInterface $request): ResponseInterface;
}
