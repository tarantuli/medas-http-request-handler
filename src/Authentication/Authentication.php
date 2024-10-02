<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Authentication;

class Authentication
{
    public function __construct(
        public object|null $user = null,
    )
    {
    }
}
