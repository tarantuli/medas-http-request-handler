<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseHandlerManager;

use Medas\HttpRequestHandler\Request\Request;

class OutputData
{
    public array $headers = [];
    public int $responseCode = 200;
    public string $output = '';

    public function __construct(
        public Request $request,
    )
    {
    }
}
