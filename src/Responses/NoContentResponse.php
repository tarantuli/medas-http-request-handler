<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Responses;

use Medas\HttpRequestHandler\ResponseTypes\{HtmlResponse, JsonResponse, SetsResponseCode};

readonly class NoContentResponse implements HtmlResponse, JsonResponse, SetsResponseCode
{
    public function getHtmlResponse(): string
    {
        return '';
    }

    public function getJsonResponse(): string
    {
        return '';
    }

    public function responseCode(): int
    {
        return 204;
    }
}
