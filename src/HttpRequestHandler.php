<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler;

use Medas\HttpRequestHandler\Exceptions\NoRequestHandlerFoundException;
use Medas\HttpRequestHandler\Request\RequestDataManager;
use Medas\Routing\HandlerManager;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class HttpRequestHandler
{
    public function __construct(
        private readonly RequestDataManager     $requestDataManager,
        private readonly ResponseHandlerManager $responseHandlerManager,
        private readonly HandlerManager         $requestHandlerManager,
    )
    {
    }

    public function handle(): void
    {
        $request = $this->requestDataManager->get();

        try {
            $requestHandler = $this->requestHandlerManager->find($request->method->value, $request->uri->endpoint);

            if ($requestHandler === null) {
                throw new NoRequestHandlerFoundException($request->method, $request->uri);
            }

            $response = $requestHandler->handle($request->method->value, $request->uri->endpoint);
            $this->responseHandlerManager->handleResponse($request, $response);
        }
        catch (\Exception|\TypeError|\Error $exception) {
            $this->responseHandlerManager->handleException($request, $exception);
        }
    }
}
