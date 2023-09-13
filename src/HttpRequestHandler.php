<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler;

use Medas\Core\Attributes\Service;
use Medas\Core\Interfaces\RoutedRequestHandlerManager;
use Medas\HttpRequestHandler\Exceptions\NoRequestHandlerFound;
use Medas\HttpRequestHandler\Request\RequestDataManager;

#[Service]
readonly class HttpRequestHandler
{
    public function __construct(
        private RequestDataManager          $requestDataManager,
        private ResponseHandlerManager      $responseHandlerManager,
        private RoutedRequestHandlerManager $routedRequestHandlerManager,
    )
    {
    }

    public function handle(): void
    {
        $request = $this->requestDataManager->get();

        try {
            $requestHandler = $this->routedRequestHandlerManager->find($request->method->value, $request->uri->endpoint);

            if ($requestHandler === null) {
                throw new NoRequestHandlerFound($request->method, $request->uri);
            }

            $response = $requestHandler->handle($request->method->value, $request->uri->endpoint);
            $this->responseHandlerManager->handleResponse($request, $response);
        }
        catch (\Exception|\TypeError|\Error $exception) {
            $this->responseHandlerManager->handleException($request, $exception);
        }
    }
}
