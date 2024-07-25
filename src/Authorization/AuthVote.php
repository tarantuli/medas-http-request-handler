<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Authorization;

use Medas\Core\Interfaces\HttpRequestHandler;
use Medas\HttpRequestHandler\Request\Request;
use Psr\EventDispatcher\StoppableEventInterface;

class AuthVote implements StoppableEventInterface
{
    public bool|null $allowedAccess = null;
    public bool $stopPropagation = false;

    public function __construct(
        public readonly Request            $request,
        public readonly HttpRequestHandler $requestHandler,
    )
    {
    }

    public function isPropagationStopped(): bool
    {
        return $this->stopPropagation;
    }
}
