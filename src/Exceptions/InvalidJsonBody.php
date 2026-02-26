<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Exceptions;

class InvalidJsonBody extends BadRequest
{
    public function __construct($rawBody)
    {
        parent::__construct($rawBody);
    }

    public function pattern(): string
    {
        return 'Invalid JSON body "%s"';
    }
}
