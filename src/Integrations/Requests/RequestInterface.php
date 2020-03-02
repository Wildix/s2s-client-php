<?php

namespace Wildix\Integrations\Requests;

use Psr\Http\Message\RequestInterface as BaseRequest;

interface RequestInterface extends BaseRequest
{
    public function getBody(): ?array;
}
