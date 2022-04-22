<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseTypes;

interface JsonLdResponse extends Response
{
    public function getJsonLdResponse(): mixed;
}
