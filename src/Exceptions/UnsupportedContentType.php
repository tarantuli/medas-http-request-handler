<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Exceptions;

class UnsupportedContentType extends BadRequest
{
    public function __construct(string $contentType)
    {
        parent::__construct($contentType);
    }

    public function pattern(): string
    {
        return 'Unsupported content type %s';
    }
}
