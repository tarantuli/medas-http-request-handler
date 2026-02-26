<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Exceptions;

use Medas\HttpRequestHandler\ResponseTypes\Response;

class DoesNotImplementJsonLdResponse extends BadRequest
{
    public function __construct(Response $response)
    {
        parent::__construct($response::class);
    }

    public function pattern(): string
    {
        return 'JSON LD response requested, but %s does not implement JsonLdResponse';
    }
}
