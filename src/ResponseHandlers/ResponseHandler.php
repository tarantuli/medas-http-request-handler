<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseHandlers;

use Medas\HttpRequestHandler\{Request\Request, ResponseHandlerManager, ResponseTypes\Response};

interface ResponseHandler
{
    public function priority(): int;

    public function handleResponse(Request $request, Response $response, ResponseHandlerManager $manager): bool;

    public function handleException(Request $request, \Throwable $exception, ResponseHandlerManager $manager): bool;
}
