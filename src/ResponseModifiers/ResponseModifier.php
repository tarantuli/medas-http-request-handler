<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseModifiers;

use Medas\Core\Interfaces\DeclaresPriority;
use Medas\HttpRequestHandler\ResponseDispatcher\{ExceptionJob, Job};

interface ResponseModifier extends DeclaresPriority
{
    public function handle(Job|ExceptionJob $job): void;

    /** Modifiers with higher values are executed first */
    public function priority(): int;
}
