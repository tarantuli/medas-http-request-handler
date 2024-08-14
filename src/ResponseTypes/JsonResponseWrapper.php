<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseTypes;

readonly class JsonResponseWrapper implements JsonResponse
{
    public function __construct(
        private mixed $data,
    )
    {
    }

    public function getJsonResponse(): mixed
    {
        return $this->data;
    }
}
