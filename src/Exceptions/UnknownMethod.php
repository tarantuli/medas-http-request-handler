<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Exceptions;

class UnknownMethod extends BadRequest
{
    public function __construct(string $name)
    {
        parent::__construct($name);
    }

    public function pattern(): string
    {
        return 'Unknown method named %s';
    }
}
