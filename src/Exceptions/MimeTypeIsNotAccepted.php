<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Exceptions;

class MimeTypeIsNotAccepted extends BadRequest
{
    public function __construct(string $mimeType)
    {
        parent::__construct($mimeType);
    }

    public function pattern(): string
    {
        return 'client does not accept mimetype %s';
    }
}
