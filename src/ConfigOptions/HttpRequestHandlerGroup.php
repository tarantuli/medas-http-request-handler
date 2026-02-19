<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup};

#[Service]
readonly class HttpRequestHandlerGroup implements ConfigGroup
{
    public function parent(): ConfigGroup|null
    {
        return null;
    }

    public function name(): string
    {
        return 'http-request-handler';
    }
}
