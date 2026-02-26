<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Request;

use Medas\Core\Attributes\Service;
use Medas\Json\JsonEncoder;

#[Service]
readonly class ChromeFetchParser
{
    public function __construct(
        private JsonEncoder $jsonEncoder,
        private UriManager  $uriManager,
    )
    {
    }

    public function get(string $fetch): Request
    {
        return cache([static::class, $fetch], fn() => $this->parse($fetch));
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
            $this->uriManager->fromString($host['uri'] ?? '/'),
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
        $body = $params['body'] ?? [];

        if (is_string($body)) {
            $body = $this->jsonEncoder->decode($body);
        }

        return $body;
    }
}
