<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseHandlers;

use Medas\HttpRequestHandler\Request\Request;
use Medas\HttpRequestHandler\ResponseTypes\JsonResponse;
use Medas\HttpRequestHandler\ResponseTypes\Response;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class JsonHandler implements ResponseHandler
{
    public function priority(): int
    {
        return -1;
    }

    public function handleResponse(Request $request, Response $response): bool
    {
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        if (!$request->serverData->acceptsMimeType('application/json') || !$response instanceof JsonResponse) {
            return false;
        }

        echo json_encode($response->getJsonResponse());
        return true;
    }

    public function handleException(Request $request, \Exception|\TypeError|\Error $exception): bool
    {
        if (!$request->serverData->acceptsMimeType('application/json')) {
            return false;
        }

        echo json_encode([
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'fileName' => $exception->getFile(),
            'lineNumber' => $exception->getLine(),

        ]);

        return true;
    }
}
