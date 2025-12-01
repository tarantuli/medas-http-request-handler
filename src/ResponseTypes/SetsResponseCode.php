<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseTypes;

interface SetsResponseCode
{
    public function responseCode(): int;
}
