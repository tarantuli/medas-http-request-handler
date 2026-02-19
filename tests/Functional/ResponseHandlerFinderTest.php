<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandlerTest\Functional;

use Medas\HttpRequestHandler\{
    ResponseHandlerRegistry,
    ResponseHandlers\FileHandler,
    ResponseHandlers\HtmlHandler,
    ResponseHandlers\JsonHandler,
    ResponseHandlers\JsonLdHandler
};
use PHPUnit\Framework\TestCase;

class ResponseHandlerFinderTest extends TestCase
{
    public function testGetHandlers(): void
    {
        $handlers = service(ResponseHandlerRegistry::class)->get();

        self::assertInstanceOf(JsonLdHandler::class, $handlers[0]);
        self::assertInstanceOf(JsonHandler::class, $handlers[1]);
        self::assertInstanceOf(HtmlHandler::class, $handlers[2]);
        self::assertInstanceOf(FileHandler::class, $handlers[3]);
    }
}
