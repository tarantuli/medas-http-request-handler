<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseDispatcher;

use Medas\HttpRequestHandler\Request\Request;

class OutputData
{
    /** @var array<string, string|array<string>> */
    private array $headers = [];

    public int $responseCode = 200;
    public string $output = '';

    public function __construct(
        public Request $request,
    )
    {
    }

    public function addHeader(string $name, string $value): void
    {
        $this->headers[$name][] = $value;
    }

    public function setHeader(string $name, string $value): void
    {
        $this->headers[$name] = [$value];
    }

    public function headers(): array
    {
        return $this->headers;
    }
}
