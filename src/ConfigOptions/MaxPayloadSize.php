<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup, Interfaces\ConfigOption};

#[Service]
readonly class MaxPayloadSize implements ConfigOption
{
    public function __construct(
        private HttpRequestHandlerGroup $group,
    )
    {
    }

    public function group(): ConfigGroup
    {
        return $this->group;
    }

    public function name(): string
    {
        return 'max-payload-size';
    }

    public function description(): string
    {
        return 'The maximum payload (upload) size in bytes';
    }

    public function hasDefault(): bool
    {
        return true;
    }

    public function default(): int
    {
        // 10 Mb
        return 10 * 1024 * 1024;
    }
}
