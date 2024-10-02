<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Request;

use Medas\Core\{Attributes\Service, Interfaces\EventDispatcher};
use Medas\HttpRequestHandler\Authentication\AuthenticationVote;

#[Service]
readonly class AuthenticationFinder
{
    public function __construct(
        public EventDispatcher $eventDispatcher,
    )
    {
    }

    public function find(Request $request): void
    {
        $authenticationVote = new AuthenticationVote($request);

        $this->eventDispatcher->dispatch($authenticationVote);

        $request->authentication->user = $authenticationVote->user;
    }
}
