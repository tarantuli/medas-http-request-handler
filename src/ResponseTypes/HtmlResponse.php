<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\ResponseTypes;

interface HtmlResponse extends Response
{
    public function outputHtmlResponse(): void;
}
