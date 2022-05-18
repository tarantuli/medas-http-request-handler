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
    private array $headers = [];

    public function __construct(
        private readonly ResponseHandlerFinder $handlerFinder,
    )
    {
    }

    public function handleResponse(Request $request, Response $response): void
    {
        ob_start();
        foreach ($this->handlerFinder->get() as $responseHandler) {
            if ($responseHandler->handleResponse($request, $response, $this)) {
                $this->printOutput();
                return;
            }
        }

        // Dump the open output buffer before throwing the exception
        ob_end_clean();
        throw new CannotHandleResponseException($response);
    }

    public function handleException(Request $request, \Exception|\TypeError|\Error $exception): void
    {
        ob_start();
        foreach ($this->handlerFinder->get() as $responseHandler) {
            if ($responseHandler->handleException($request, $exception, $this)) {
                $this->printOutput();
                return;
            }
        }

        // Dump the open output buffer before outputting a default exception message
        ob_end_clean();
        printf("%s:%u [%u]] %s\n", $exception->getFile(), $exception->getLine(), $exception->getCode(), $exception->getMessage());
    }

    public function setHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }

    private function printOutput(): void
    {
        if (!headers_sent()) {
            foreach ($this->headers as $name => $value) {
                header(sprintf('%s: %s', $name, $value));
            }
        }

        ob_end_flush();
    }
}
