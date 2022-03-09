<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseHandlers;

use Medas\HttpRequestHandler\Request\Request;
use Medas\HttpRequestHandler\ResponseTypes\Response;

interface ResponseHandler
{
    public function priority(): int;

    public function handleResponse(Request $request, Response $response): bool;

    public function handleException(Request $request, \Exception|\TypeError|\Error $exception): bool;
}
