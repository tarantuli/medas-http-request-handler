<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Exceptions;

class FailedReadRequestBody extends BadRequest
{
    public function pattern(): string
    {
        return 'Failed to read request body';
    }
}
