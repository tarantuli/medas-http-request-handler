<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler;

use Medas\Core\{Attributes\Service, Events\DebugInformation};

#[Service]
readonly class ResponseDispatcher
{
    public function __construct(
        private ResponseDispatcher\OutputDataPrinter $outputDataPrinter,
        private ResponseHandlerRegistry              $responseHandlerRegistry,
        private ResponseHandlers\CorsHeaderWriter    $corsHeaderWriter,
    )
    {
    }

    /**
     * Builds a fully populated Job (CORS headers applied, response handler run, output set)
     * without sending any HTTP output. Safe to call in test contexts.
     *
     * @throws Exceptions\CannotHandleResponseType
     */
    public function prepareJob(Request\Request $request, ResponseTypes\Response $response): ResponseDispatcher\Job
    {
        $job = new ResponseDispatcher\Job($request, $response);

        $this->corsHeaderWriter->handle($job);

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

                return $job;
            }
        }

        throw new Exceptions\CannotHandleResponseType($response);
    }

    public function handleResponse(Request\Request $request, ResponseTypes\Response $response): void
    {
        $job = $this->prepareJob($request, $response);

        $this->outputDataPrinter->print($job);
    }
}
