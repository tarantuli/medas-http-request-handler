<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Request;

use Medas\Core\Attributes\Service;

#[Service]
class UriManager
{
    public function fromString(string $string): Uri
    {
        $uri = new Uri($string);

        if (false !== $pos = strpos($uri->endpoint, '?')) {
            parse_str(substr($uri->endpoint, $pos + 1), $uri->query);
            $uri->endpoint = substr($uri->endpoint, 0, $pos);
        }

        if (preg_match('/^(.+)\.(\w+)$/', $uri->endpoint, $parts)) {
            $uri->endpoint = $parts[1];
            $uri->extension = mb_strtolower($parts[2]);
        }

        if (strlen($uri->endpoint) > 1) {
            $uri->endpoint = rtrim($uri->endpoint, '/');
        }

        return $uri;
    }
}
