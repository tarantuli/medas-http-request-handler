<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Exceptions;

class PayloadTooLarge extends BadRequest implements DeclaresResponseCode
{
    public function __construct(int $size, int $maxSize)
    {
        parent::__construct($size, $maxSize);
    }

    public function pattern(): string
    {
        return 'The body is too large, %s bytes, %s bytes are allowed';
    }

    public function responseCode(): int
    {
        return 413;
    }
}
