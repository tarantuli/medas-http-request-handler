<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler;

use Medas\Core\{Attributes\Service, Interfaces\RoutedRequestHandlerManager};

#[Service]
readonly class HttpRequestHandler
{
    public function __construct(
        private Request\RequestDataManager  $requestDataManager,
        private ResponseHandlerManager      $responseHandlerManager,
        private RoutedRequestHandlerManager $routedRequestHandlerManager,
    )
    {
    }

    public function handle(): void
    {
        $request = $this->requestDataManager->get();

        try {
            $requestHandler = $this->routedRequestHandlerManager->find(
                $request->method->value,
                $request->uri->endpoint
            );

            if ($requestHandler === null) {
                throw new Exceptions\NoRequestHandlerFound($request->method, $request->uri);
            }

            $response = $requestHandler->handle($request->method->value, $request->uri->endpoint);

            $this->responseHandlerManager->handleResponse($request, $response);
        }
        catch (\Exception|\TypeError|\Error $exception) {
            $this->responseHandlerManager->handleException($request, $exception);
        }
    }
}
