<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseHandlers;

use Medas\HttpRequestHandler\Exceptions\NoJsonLdResponseException;
use Medas\HttpRequestHandler\Request\Request;
use Medas\HttpRequestHandler\ResponseTypes\JsonLdResponse;
use Medas\HttpRequestHandler\ResponseTypes\Response;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class JsonLdHandler implements ResponseHandler
{
    public function priority(): int
    {
        return -5;
    }

    public function handleResponse(Request $request, Response $response): bool
    {
        if ($request->uri->extension === 'jsonld') {
            if (!$response instanceof JsonLdResponse) {
                throw new NoJsonLdResponseException($response);
            }

            // Else, fall through to the echo command
        }
        elseif (!$request->serverData->acceptsMimeType('application/ld+json') || !$response instanceof JsonLdResponse) {
            return false;
        }

        header('Content-Type: application/ld+json');
        header('Access-Control-Allow-Origin: *');

        echo json_encode($response->getJsonLdResponse());
        return true;
    }

    public function handleException(Request $request, \Exception|\TypeError|\Error $exception): bool
    {
        if (!$request->serverData->acceptsMimeType('application/ld+json')) {
            return false;
        }

        // todo: craft a real jsonld error response
        header('Content-Type: application/ld+json');
        header('Access-Control-Allow-Origin: *');

        echo json_encode([
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'fileName' => $exception->getFile(),
            'lineNumber' => $exception->getLine(),

        ]);

        return true;
    }
}
