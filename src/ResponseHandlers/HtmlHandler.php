<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseHandlers;

use Medas\HttpRequestHandler\Request\Method;
use Medas\HttpRequestHandler\Request\Request;
use Medas\HttpRequestHandler\ResponseHandlerManager;
use Medas\HttpRequestHandler\ResponseTypes\HtmlResponse;
use Medas\HttpRequestHandler\ResponseTypes\Response;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class HtmlHandler implements ResponseHandler
{
    public function priority(): int
    {
        return -20;
    }

    public function handleResponse(Request                $request,
                                   Response               $response,
                                   ResponseHandlerManager $manager): bool
    {
        if (!$response instanceof HtmlResponse) {
            return false;
        }

        if ($request->method !== Method::Options && !$request->serverData->acceptsMimeType('text/html')) {
            return false;
        }

        $response->outputHtmlResponse();
        return true;
    }

    public function handleException(Request                      $request,
                                    \Exception|\TypeError|\Error $exception,
                                    ResponseHandlerManager       $manager): bool
    {
        if (!$request->serverData->acceptsMimeType('text/html')) {
            return false;
        }

        printf('<p>%s:%u [%u] %s</p>', $exception->getFile(), $exception->getLine(), $exception->getCode(), $exception->getMessage());
        return true;
    }
}
