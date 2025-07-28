<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseHandlers;

use Medas\HttpRequestHandler\ResponseHandlerManager\{ExceptionJob, Job};

interface ResponseHandler
{
    public function priority(): int;

    public function handleResponse(Job $job): bool;

    public function handleException(ExceptionJob $job): bool;
}
