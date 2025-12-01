<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler;

use Medas\Core\{Attributes\Service, Events\DebugInformation};
use Medas\ServiceManager\ErrorHandling\ExceptionHandler;

#[Service]
readonly class ResponseHandlerManager implements ExceptionHandler
{
    public function __construct(
        private ExceptionHandlerManager                  $exceptionHandlerManager,
        private ResponseHandlerManager\OutputDataPrinter $outputDataPrinter,
        private ResponseHandlerFinder                    $handlerFinder,
    )
    {
    }

    public function handleResponse(Request\Request $request, ResponseTypes\Response $response): void
    {
        $job = new ResponseHandlerManager\Job($request, $response);

        if ($response instanceof ResponseTypes\SetsResponseCode) {
            $job->responseCode = $response->responseCode();

            dispatch(new DebugInformation(
                '[response-handler-manager] response code set to %d from response data',
                $job->responseCode
            ));
        }

        foreach ($this->handlerFinder->get() as $responseHandler) {
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
        $this->exceptionHandlerManager->handle($exception);
    }
}
