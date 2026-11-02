<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Exceptions;

/**
 * A body or query argument doesn't fit the type of the parameter it's injected into
 */
class InvalidRequestArgument extends BadRequest
{
    public function __construct(string $argument, string $reason)
    {
        parent::__construct($argument, $reason);
    }

    public function pattern(): string
    {
        return 'request argument %s is invalid: %s';
    }
}
