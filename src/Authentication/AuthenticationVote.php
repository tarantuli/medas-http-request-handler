<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Authentication;

use Medas\HttpRequestHandler\Request\Request;
use Psr\EventDispatcher\StoppableEventInterface;

class AuthenticationVote implements StoppableEventInterface
{
    public object|null $user = null;

    public function __construct(
        public Request $request,
    )
    {
    }

    public function isPropagationStopped(): bool
    {
        return $this->user !== null;
    }
}
