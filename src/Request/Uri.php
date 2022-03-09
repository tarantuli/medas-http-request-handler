<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Request;

class Uri
{
    public string|null $extension = null;
    public string $endpoint;
    public string|null $query = null;

    public function __construct(
        public string $uri,
    )
    {
        $this->endpoint = $this->uri;
    }
}
