<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler;

use Medas\HttpRequestHandler\Exceptions\CannotHandleResponseException;
use Medas\HttpRequestHandler\Request\Request;
use Medas\HttpRequestHandler\ResponseTypes\Response;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class ResponseHandlerManager
{
    public function __construct(
        private ResponseHandlerFinder $handlerFinder,
    )
    {
    }

    public function handleResponse(Request $request, Response $response): void
    {
        foreach ($this->handlerFinder->get() as $responseHandler) {
            if ($responseHandler->handleResponse($request, $response)) {
                return;
            }
        }

        throw new CannotHandleResponseException($response);
    }

    public function handleException(Request $request, \Exception|\TypeError|\Error $exception): void
    {
        foreach ($this->handlerFinder->get() as $responseHandler) {
            if ($responseHandler->handleException($request, $exception)) {
                return;
            }
        }

        printf("%s:%u [%u]] %s\n", $exception->getFile(), $exception->getLine(), $exception->getCode(), $exception->getMessage());
    }
}
