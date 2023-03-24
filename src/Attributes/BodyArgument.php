<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Attributes;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class BodyArgument
{
    public function __construct(
        public readonly string $name,
    )
    {
    }
}
