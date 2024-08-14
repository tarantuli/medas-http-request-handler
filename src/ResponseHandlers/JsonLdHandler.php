<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseHandlers;

use Medas\Core\Attributes\Service;
use Medas\HttpRequestHandler\{
    Exceptions\DoesNotImplementJsonLdResponse,
    Request\Request,
    ResponseHandlerManager,
    ResponseTypes\JsonLdResponse,
    ResponseTypes\Response
};
use Medas\Json\JsonEncoder;

#[Service]
readonly class JsonLdHandler implements ResponseHandler
{
    public function __construct(
        private JsonEncoder $jsonEncoder,
    )
    {
    }

    public function priority(): int
    {
        return -5;
    }

    public function handleResponse(Request $request, Response $response, ResponseHandlerManager $manager): bool
    {
        if ($request->uri->extension === 'jsonld') {
            if (!$response instanceof JsonLdResponse) {
                throw new DoesNotImplementJsonLdResponse($response);
            }

            // Else, fall through to the echo command
        }
        elseif (
            !$request->serverData->acceptsMimeType('application/ld+json')
            || !$response instanceof JsonLdResponse
        ) {
            return false;
        }

        $manager->setHeader('Content-Type', 'applicationld+json');
        $manager->setHeader('Access-Control-Allow-Origin', '*');

        echo $this->jsonEncoder->encode($response->getJsonLdResponse());

        return true;
    }

    public function handleException(
        Request                      $request,
        \Exception|\TypeError|\Error $exception,
        ResponseHandlerManager       $manager
    ): bool
    {
        if (!$request->serverData->acceptsMimeType('application/ld+json')) {
            return false;
        }

        // todo: craft a real jsonld error response
        $manager->setHeader('Content-Type', 'application/ld+json');
        $manager->setHeader('Access-Control-Allow-Origin', '*');

        echo $this->jsonEncoder->encode([
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'fileName' => $exception->getFile(),
            'lineNumber' => $exception->getLine(),
        ]);

        return true;
    }
}
