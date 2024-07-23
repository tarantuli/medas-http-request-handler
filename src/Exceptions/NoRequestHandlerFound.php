<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Exceptions;

use Medas\HttpRequestHandler\Request\{Method, Uri};

class NoRequestHandlerFound extends BadRequest
{
    public function __construct(Method $method, Uri $uri)
    {
        parent::__construct($method->value, $uri->endpoint);
    }

    public function pattern(): string
    {
        return 'No request handler found for method %s and endpoint %s';
    }
}
