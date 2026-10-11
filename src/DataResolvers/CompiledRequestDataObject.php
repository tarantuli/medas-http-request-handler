<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\DataResolvers;

use Medas\HttpRequestHandler\Attributes\RequestDataObject;

/**
 * The compiled form of a parameter that @class(RequestDataObjectResolver) resolves.
 */
readonly class CompiledRequestDataObject
{
    public function __construct(
        public RequestDataObject $attribute,
        public string            $className,
    )
    {
    }
}
