<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler;

use Medas\Core\{Attributes\Service, Events\DebugInformation, Interfaces\HttpRequestHandlerManager};

#[Service]
readonly class HttpRequestHandler
{
    public function __construct(
        private HttpRequestHandlerManager $httpRequestHandlerManager,
        private RequestFactory            $requestFactory,
        private ResponseDispatcher        $responseDispatcher,
    )
    {
    }

    public function handle(): void
    {
        $request = $this->requestFactory->get();
        $response = $this->processRequest($request);

        $this->responseDispatcher->handleResponse($request, $response);
    }

    public function processRequest(Request\Request $request): ResponseTypes\Response
    {
        dispatch(new DebugInformation(
            '[http-request-handler] handling request: %s %s',
            $request->method->value,
            $request->uri
        ));

        if ($request->postData->count()) {
            dispatch(new DebugInformation('[http-request-handler] POST data: %s', $request->postData));
        }

        if ($request->bodyData->data()) {
            dispatch(new DebugInformation('[http-request-handler] body data: %s', $request->bodyData->data()));
        }

        $requestHandler = $this->httpRequestHandlerManager->find(
            $request->method->value,
            $request->uri->endpoint
        );

        if ($requestHandler === null) {
            throw new Exceptions\NoRequestHandlerFound($request->method, $request->uri);
        }

        dispatch(new DebugInformation('[http-request-handler] found handler: %s', $requestHandler::class));

        $authVote = new Authorization\AuthorizationVote($request, $requestHandler);

        dispatch($authVote);

        dispatch(new DebugInformation(
            '[http-request-handler] authorization vote result: %s',
            $authVote->allowedAccess
        ));

        if ($authVote->allowedAccess !== true) {
            throw new Exceptions\RequestNotAuthorized($authVote->allowedAccess);
        }

        return $requestHandler->handle($request->method->value, $request->uri->endpoint);
    }
}
