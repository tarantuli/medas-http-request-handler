<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseHandlers;

use Medas\Core\Interfaces\DeclaresPriority;
use Medas\HttpRequestHandler\ResponseDispatcher\{ExceptionJob, Job};

interface ResponseHandler extends DeclaresPriority
{
    public function priority(): int;

    public function handleResponse(Job $job): bool;

    public function handleException(ExceptionJob $job): bool;
}
