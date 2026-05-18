<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseDispatcher;

use Medas\HttpRequestHandler\Request\Request;

class OutputData
{
    /** @var array<string, string|array<string>> */
    public array $headers = [];

    public int $responseCode = 200;
    public string $output = '';

    public function __construct(
        public Request $request,
    )
    {
    }
}
