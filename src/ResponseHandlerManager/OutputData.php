<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseHandlerManager;

class OutputData
{
    public array $headers = [];
    public int $responseCode = 200;
    public string $output;
}
