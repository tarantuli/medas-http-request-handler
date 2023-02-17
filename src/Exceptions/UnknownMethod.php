<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Exceptions;

use Medas\Core\Exceptions\BaseException;

class UnknownMethod extends BaseException
{
    public function __construct(string $name)
    {
        parent::__construct($name);
    }

    public function pattern(): string
    {
        return 'Unknown method named %s';
    }
}
