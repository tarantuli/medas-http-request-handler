<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Request;

readonly class Uri
{
    public function __construct(
        public string      $uri,
        public string|null $extension,
        public string      $endpoint,
        public array       $query,
    )
    {
    }
}
