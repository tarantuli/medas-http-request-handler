<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseTypes;

interface JsonResponse extends Response
{
    public function outputJsonResponse(): void;
}
