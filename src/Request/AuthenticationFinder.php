<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Request;

use Medas\Core\Attributes\Service;
use Medas\HttpRequestHandler\Authentication\AuthenticationVote;

#[Service]
readonly class AuthenticationFinder
{
    public function find(Request $request): void
    {
        $authenticationVote = new AuthenticationVote($request);

        dispatch($authenticationVote);

        $request->authentication->user = $authenticationVote->user;
    }
}
