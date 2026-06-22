<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Attributes;

/**
 * A parameter with this attribute must have a class type. Its value will be a newly instantiated object of that type,
 * filled with values from the request query
 */
#[\Attribute(\Attribute::TARGET_PARAMETER)]
readonly class QueryDataObject extends RequestDataObject
{
    public function __construct()
    {
        parent::__construct(false);
    }
}
