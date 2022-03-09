<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandlerTest\Functional;

use Medas\HttpRequestHandler\Exceptions\CannotHandleResponseException;
use Medas\HttpRequestHandler\Request\ChromeFetchParser;
use Medas\HttpRequestHandler\Request\Request;
use Medas\HttpRequestHandler\ResponseHandlerManager;
use Medas\HttpRequestHandlerTest\MockUps\ChromeFetches;
use Medas\HttpRequestHandlerTest\MockUps\Responses\JsonTestResponse;
use PHPUnit\Framework\TestCase;

class ResponseHandlerManagerTest extends TestCase
{
    public function testCannotHandle(): void
    {
        $manager = service(ResponseHandlerManager::class);
        $request = $this->getHtmlRequest();
        $response = new JsonTestResponse();

        self::expectException(CannotHandleResponseException::class);
        $manager->handleResponse($request, $response);
    }

    private function getHtmlRequest(): Request
    {
        return service(ChromeFetchParser::class)->get(ChromeFetches::instance()->getHtml());
    }

    public function testJsonHandler(): void
    {
        $manager = service(ResponseHandlerManager::class);
        $request = $this->getJsonRequest();
        $response = new JsonTestResponse();

        ob_start();
        $manager->handleResponse($request, $response);
        $output = ob_get_clean();

        self::assertEquals('{"data":"value"}', $output);
    }

    private function getJsonRequest(): Request
    {
        return service(ChromeFetchParser::class)->get(ChromeFetches::instance()->getJson());
    }
}
