<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Exceptions;

use Medas\Core\{Exceptions\BaseException, Interfaces\BadRequestException};

abstract class BadRequest extends BaseException implements BadRequestException
{
}
