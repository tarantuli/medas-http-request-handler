<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Authorization;

use Psr\EventDispatcher\StoppableEventInterface;

abstract class BasicVote implements StoppableEventInterface
{
    public bool|null $allowedAccess = null;

    public function isPropagationStopped(): bool
    {
        return $this->allowedAccess !== null;
    }
}
