<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler;

use Medas\Core\{Attributes\Service, Events\DebugInformation, Interfaces\ExceptionHandler};

#[Service]
readonly class ResponseDispatcher implements ExceptionHandler
{
    public function __construct(
        private ExceptionHandler                     $exceptionHandler,
        private ResponseDispatcher\OutputDataPrinter $outputDataPrinter,
        private ResponseHandlerRegistry              $responseHandlerRegistry,
    )
    {
    }

    public function handleResponse(Request\Request $request, ResponseTypes\Response $response): void
    {
        $job = new ResponseDispatcher\Job($request, $response);

        if ($response instanceof ResponseTypes\SetsResponseCode) {
            $job->responseCode = $response->responseCode();

            dispatch(new DebugInformation(
                '[response-handler-manager] response code set to %s from response data',
                $job->responseCode
            ));
        }

        foreach ($this->responseHandlerRegistry->get() as $responseHandler) {
            if ($responseHandler->handleResponse($job)) {
                dispatch(new DebugInformation('[response-handler-manager] found handler: %s', $responseHandler::class));

                $this->outputDataPrinter->print($job);

                return;
            }
        }

        throw new Exceptions\CannotHandleResponseType($response);
    }

    public function handleException(\Throwable $exception): void
    {
        $this->exceptionHandler->handleException($exception);
    }
}
