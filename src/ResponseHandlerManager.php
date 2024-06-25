<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler;

use Medas\Core\Attributes\Service;

#[Service]
class ResponseHandlerManager
{
    private array $headers = [];

    public function __construct(
        private readonly ResponseHandlerFinder $handlerFinder,
    )
    {
    }

    public function handleResponse(Request\Request $request, ResponseTypes\Response $response): void
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

        throw new Exceptions\CannotHandleResponseType($response);
    }

    public function handleException(Request\Request $request, \Exception|\TypeError|\Error $exception): void
    {
        $responseCode = $exception instanceof Exceptions\BadRequest ? 400 : 500;

        http_response_code($responseCode);

        try {
            ob_start();

            foreach ($this->handlerFinder->get() as $responseHandler) {
                if ($responseHandler->handleException($request, $exception, $this)) {
                    $this->printOutput();

                    return;
                }
            }

            $this->lastEffortExceptionPrinting($exception);
        }
        catch (\Exception|\TypeError|\Error) {
            $this->lastEffortExceptionPrinting($exception);
        }
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

    private function lastEffortExceptionPrinting(\Exception|\TypeError|\Error $exception): void
    {
        // Dump the open output buffer before outputting a default exception message
        ob_end_clean();

        if (isset($_SERVER['HTTP_HOST'])) {
            echo '<pre>';
        }

        foreach (array_reverse($exception->getTrace()) as $trace) {
            if (isset($trace['file'])) {
                printf(
                    "%s:%u\n   %s::%s()\n",
                    $trace['file'],
                    $trace['line'],
                    $trace['class'] ?? '[main]',
                    $trace['function']
                );
            }
            else {
                printf("[main]\n   %s::%s()\n", $trace['class'] ?? '[main]', $trace['function']);
            }

            foreach ($trace['args'] ?? [] as $index => $argument) {
                if (is_string($argument) && mb_detect_encoding($argument, 'UTF-8')) {
                    printf("    %u: %s\n", $index, mb_substr($argument, 0, 78));
                }
                else {
                    printf(
                        "    %u: %s(%u)\n",
                        $index,
                        get_debug_type($argument),
                        is_string($argument) ? strlen($argument) : 0
                    );
                }
            }

            printf("\n");
        }

        printf(
            "\n%s:%u [%u]\n%s\n\n",
            $exception->getFile(),
            $exception->getLine(),
            $exception->getCode(),
            $exception->getMessage()
        );

        if (isset($_SERVER['HTTP_HOST'])) {
            echo '</pre>';
        }
    }

    public function setHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }
}
