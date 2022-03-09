<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandler\Request;

enum Method: string
{
    case Get = 'GET';
    case Post = 'POST';
    case Put = 'PUT';
    case Delete = 'DELETE';
}
