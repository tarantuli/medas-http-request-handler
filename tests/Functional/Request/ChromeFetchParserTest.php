<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandlerTest\Functional\Request;

use Medas\HttpRequestHandler\Request\{ChromeFetchParser, Method};
use Medas\HttpRequestHandlerTest\MockUps\ChromeFetches;
use PHPUnit\Framework\TestCase;

class ChromeFetchParserTest extends TestCase
{
    public function testParseTextHtml(): void
    {
        $source = ChromeFetches::instance()->getHtml();
        $request = service(ChromeFetchParser::class)->get($source);

        self::assertEquals(Method::Get, $request->method);
        self::assertEquals('/process-image', $request->uri->endpoint);
        self::assertTrue($request->serverData->acceptsMimeType('text/html'));
        self::assertFalse($request->serverData->acceptsMimeType('application/json'));
    }

    public function testParseImage(): void
    {
        $source = ChromeFetches::instance()->getImage();
        $request = service(ChromeFetchParser::class)->get($source);

        self::assertEquals(Method::Get, $request->method);
        self::assertTrue($request->serverData->acceptsMimeType('image/png'));
    }
}
