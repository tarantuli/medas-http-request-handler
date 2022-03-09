<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseTypes;

interface FileResponse extends Response
{
    public function outputFileResponse(): void;
}
