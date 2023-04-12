<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Request;

use Medas\ServiceManager\Cache\CacheManager;
use Medas\ServiceManager\Service;

#[Service]
class ChromeFetchParser
{
    public function __construct(
        private readonly CacheManager $cacheManager,
        private readonly UriManager   $uriManager,
    )
    {
    }

    public function get(string $fetch): Request
    {
        return $this->cacheManager->get()->get([static::class, $fetch], fn() => $this->parse($fetch));
    }

    private function parse($fetch): Request
    {
        if (!preg_match('/^fetch\("(?<host>[^"]+)", (?<params>{.+})\);$/s', $fetch, $parts)) {
            throw new Exceptions\InvalidChromeFetchString($fetch);
        }

        if (!preg_match('/^(?<scheme>\w+):\/\/(?<serverName>[^\/]+)(?<uri>\/.*)?$/', $parts['host'], $host)) {
            throw new Exceptions\InvalidHostString($parts['host']);
        }

        $params = json_decode($parts['params'], true);

        return new Request(
            Method::from($params['method']),
            $this->uriManager->fromString($host['uri']),
            new ServerData($this->determineServerData($host['scheme'], $host['serverName'], $params)),
            new PostData($this->determinePostData($params)),
            new BodyData([]),
            new FileData([]),
        );
    }

    private function determineServerData(string $scheme, string $serverName, array $params): array
    {
        $data = [
            'SERVER_NAME' => $serverName,
            'SERVER_PROTOCOL' => strtoupper($scheme),
            'REQUEST_METHOD' => $params['method'],
            'HTTPS' => $scheme === 'https',
        ];

        foreach ($params['headers'] as $header => $value) {
            $key = 'HTTP_' . strtoupper((str_replace('-', '_', $header)));
            $data[$key] = $value;
        }

        return $data;
    }

    private function determinePostData(mixed $params): array
    {
        return $params['body'] ?? [];
    }
}
