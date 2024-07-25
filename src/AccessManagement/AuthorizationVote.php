<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\AccessManagement;

use Medas\Core\Interfaces\HttpRequestHandler;
use Medas\HttpRequestHandler\Request\Request;
use Psr\EventDispatcher\StoppableEventInterface;

class AuthorizationVote implements StoppableEventInterface
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
