<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Request\Exceptions;

use Medas\Core\Exceptions\BaseException;

class InvalidChromeFetchStringException extends BaseException
{
    public function __construct(string $fetch)
    {
        parent::__construct($fetch);
    }

    public function pattern(): string
    {
        return 'Invalid Chrome fetch string %s';
    }
}
