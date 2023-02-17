<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Exceptions;

use Medas\Core\Exceptions\BaseException;

class MimeTypeIsNotAccepted extends BaseException
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
