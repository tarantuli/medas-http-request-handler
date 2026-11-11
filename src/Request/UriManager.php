<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Request;

use Medas\Core\Attributes\Service;

#[Service]
readonly class UriManager
{
    // Stands in for a dot in a query name while parse_str runs; see parseQuery().
    private const string DOT_MARKER = '__medas_dot__';

    public function fromString(string $string): Uri
    {
        $endpoint = $string;
        $extension = null;
        $query = [];

        if (false !== $pos = strpos($endpoint, '?')) {
            $query = $this->parseQuery(substr($endpoint, $pos + 1));
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

    // parse_str turns a dot in a top-level name into an underscore (a leftover of
    // register_globals), so a filter on a related entity's property,
    // "products.name*=x", arrived as "products_name*". The dots are swapped for a
    // marker before and restored after, so names arrive as sent; array syntax
    // ("ids[]=1") still goes through parse_str as before.
    private function parseQuery(string $queryString): array
    {
        $protected = preg_replace_callback(
            '/(^|&)([^=&\[]*)/',
            fn(array $match) => $match[1] . str_ireplace(['.', '%2E'], self::DOT_MARKER, $match[2]),
            $queryString,
        );

        parse_str($protected, $query);

        $restored = [];

        foreach ($query as $name => $value) {
            $restored[str_replace(self::DOT_MARKER, '.', (string) $name)] = $value;
        }

        return $restored;
    }
}
