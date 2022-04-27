<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandlerTest\Functional;

use Medas\HttpRequestHandler\ResponseHandlerFinder;
use Medas\HttpRequestHandler\ResponseHandlers\{FileHandler, HtmlHandler, JsonHandler, JsonLdHandler};
use PHPUnit\Framework\TestCase;

class ResponseHandlerFinderTest extends TestCase
{
    public function testGetHandlers(): void
    {
        $handlers = service(ResponseHandlerFinder::class)->get();

        self::assertInstanceOf(JsonLdHandler::class, $handlers[0]);
        self::assertInstanceOf(JsonHandler::class, $handlers[1]);
        self::assertInstanceOf(HtmlHandler::class, $handlers[2]);
        self::assertInstanceOf(FileHandler::class, $handlers[3]);
    }
}
