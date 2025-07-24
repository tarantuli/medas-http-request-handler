<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Exceptions;

class RequestNotAuthorized extends UnauthorizedRequest
{
    public function __construct(public bool|null $voteResult)
    {
        parent::__construct($this->voteResult === false ? 'disallowed' : 'unauthorized');
    }

    public function pattern(): string
    {
        return 'this request is %s';
    }
}
