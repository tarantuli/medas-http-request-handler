<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandlerTest\MockUps\Responses;

use Medas\HttpRequestHandler\ResponseTypes\JsonResponse;

class JsonTestResponse implements JsonResponse
{
    public function getJsonResponse(): array
    {
        return ['data' => 'value'];
    }
}
