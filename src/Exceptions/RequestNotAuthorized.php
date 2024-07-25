<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Exceptions;

class RequestNotAuthorized extends BadRequest
{
    public function __construct(public bool|null $voteResult)
    {
        parent::__construct($this->voteResult === false ? 'not allowed' : 'not authorized');
    }

    public function pattern(): string
    {
        return 'this request is %s';
    }
}
