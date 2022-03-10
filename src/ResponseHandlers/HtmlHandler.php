<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseHandlers;

use Medas\HttpRequestHandler\Request\Request;
use Medas\HttpRequestHandler\ResponseTypes\HtmlResponse;
use Medas\HttpRequestHandler\ResponseTypes\Response;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class HtmlHandler implements ResponseHandler
{
    public function priority(): int
    {
        return -2;
    }

    public function handleResponse(Request $request, Response $response): bool
    {
        if (!$request->serverData->acceptsMimeType('text/html') || !$response instanceof HtmlResponse) {
            return false;
        }

        $response->outputHtmlResponse();
        return true;
    }

    public function handleException(Request $request, \Exception|\TypeError|\Error $exception): bool
    {
        if (!$request->serverData->acceptsMimeType('text/html')) {
            return false;
        }

        printf('<p>%s:%u [%u] %s</p>', $exception->getFile(), $exception->getLine(), $exception->getCode(), $exception->getMessage());
        return true;
    }
}
