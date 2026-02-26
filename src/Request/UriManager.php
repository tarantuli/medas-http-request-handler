<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Request;

use Medas\Core\Attributes\Service;

#[Service]
readonly class UriManager
{
    public function fromString(string $string): Uri
    {
        $endpoint = $string;
        $extension = null;
        $query = [];

        if (false !== $pos = strpos($endpoint, '?')) {
            parse_str(substr($endpoint, $pos + 1), $query);

            $endpoint = substr($endpoint, 0, $pos);
        }

        if (preg_match('/^(.+)\.(\w+)$/', $endpoint, $parts)) {
            $endpoint = $parts[1];
            $extension = mb_strtolower($parts[2]);
        }

        if (strlen($endpoint) > 1) {
            $endpoint = rtrim($endpoint, '/');
        }

        return new Uri($string, $extension, $endpoint, $query);
    }
}
