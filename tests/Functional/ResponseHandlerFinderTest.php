<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandlerTest\Functional;

use Medas\HttpRequestHandler\{
    ResponseHandlerRegistry,
    ResponseHandlers\FileHandler,
    ResponseHandlers\HtmlHandler,
    ResponseHandlers\JsonHandler,
    ResponseHandlers\JsonLdHandler,
    ResponseHandlers\OptionsHandler
};
use PHPUnit\Framework\TestCase;

class ResponseHandlerFinderTest extends TestCase
{
    public function testGetHandlers(): void
    {
        $handlers = iterator_to_array(service(ResponseHandlerRegistry::class)->get(), false);

        self::assertInstanceOf(OptionsHandler::class, $handlers[0]);
        self::assertInstanceOf(JsonLdHandler::class, $handlers[1]);
        self::assertInstanceOf(JsonHandler::class, $handlers[2]);
        self::assertInstanceOf(HtmlHandler::class, $handlers[3]);
        self::assertInstanceOf(FileHandler::class, $handlers[4]);
    }
}
