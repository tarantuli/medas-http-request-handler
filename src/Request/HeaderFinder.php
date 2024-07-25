<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Request;

use Medas\Core\Attributes\Service;

#[Service]
readonly class HeaderFinder
{
    public function find(ServerData $data, string $name): string|null
    {
        $key = 'HTTP_' . strtoupper((str_replace('-', '_', $name)));

        return $data[$key] ?? null;
    }
}
