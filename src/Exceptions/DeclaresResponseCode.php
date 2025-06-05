<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Exceptions;

/**
 * This HTTP request exception declares its own, specific response code
 */
interface DeclaresResponseCode
{
    public function responseCode(): int;
}
