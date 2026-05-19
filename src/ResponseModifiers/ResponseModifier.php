<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseModifiers;

use Medas\HttpRequestHandler\ResponseDispatcher\{ExceptionJob, Job};

interface ResponseModifier
{
    public function handle(Job|ExceptionJob $job): void;

    /** Modifiers with higher values are executed first */
    public function priority(): int;
}
