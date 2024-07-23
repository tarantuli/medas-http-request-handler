<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Exceptions;

class BodyArgumentIsMissing extends BadRequest
{
    public function __construct(string $argumentName)
    {
        parent::__construct($argumentName);
    }

    public function pattern(): string
    {
        return 'body argument %s is missing';
    }
}
