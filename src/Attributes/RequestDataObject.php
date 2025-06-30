<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Attributes;

/**
 * A parameter with this attribute must have a class type. Its value will be a newly instantiated object of that type,
 * filled with values from the request body and/or query
 */
#[\Attribute(\Attribute::TARGET_PARAMETER)]
readonly class RequestDataObject
{
    public function __construct(
        public bool $fromBody = true,
        public bool $fromQuery = true,
    )
    {
    }
}
