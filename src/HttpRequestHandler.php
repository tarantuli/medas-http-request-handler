<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler;

use Medas\Core\{
    Attributes\Service,
    Interfaces\EventDispatcher,
    Interfaces\HttpRequestHandlerManager
};

#[Service]
readonly class HttpRequestHandler
{
    public function __construct(
        private EventDispatcher           $eventDispatcher,
        private HttpRequestHandlerManager $httpRequestHandlerManager,
        private RequestDataManager        $requestDataManager,
        private ResponseHandlerManager    $responseHandlerManager,
    )
    {
    }

    public function handle(): void
    {
        $request = $this->requestDataManager->get();

        try {
            $requestHandler = $this->httpRequestHandlerManager->find(
                $request->method->value,
                $request->uri->endpoint
            );

            if ($requestHandler === null) {
                throw new Exceptions\NoRequestHandlerFound($request->method, $request->uri);
            }

            $authVote = new Authorization\AuthorizationVote($request, $requestHandler);

            $this->eventDispatcher->dispatch($authVote);

            if ($authVote->allowedAccess !== true) {
                throw new Exceptions\RequestNotAuthorized($authVote->allowedAccess);
            }

            $response = $requestHandler->handle($request->method->value, $request->uri->endpoint);

            $this->responseHandlerManager->handleResponse($request, $response);
        }
        catch (\Exception|\TypeError|\Error $exception) {
            $this->responseHandlerManager->handleException($request, $exception);
        }
    }
}
