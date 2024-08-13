<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Exceptions;

class QueryArgumentIsMissing extends BadRequest
{
    public function __construct(string $argumentName)
    {
        parent::__construct($argumentName);
    }

    public function pattern(): string
    {
        return 'query argument %s is missing';
    }
}
