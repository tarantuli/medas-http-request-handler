<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandlerTest\Functional\Request;

use Medas\HttpRequestHandler\Request\UriManager;
use PHPUnit\Framework\TestCase;

class UriManagerTest extends TestCase
{
    public function testBasicUsage(): void
    {
        $string = 'images.json?width=200&height=50';
        $uri = service(UriManager::class)->fromString($string);

        self::assertEquals('images', $uri->endpoint);
        self::assertEquals('json', $uri->extension);
        self::assertEquals(['width' => 200, 'height' => 50], $uri->query);
    }

    public function testDotsInNamesSurvive(): void
    {
        $uri = service(UriManager::class)->fromString('subscriptions?products.name*=IAT&page=1');

        self::assertSame(['products.name*' => 'IAT', 'page' => '1'], $uri->query);
    }

    public function testEncodedDotsAndArraysStillParse(): void
    {
        $uri = service(UriManager::class)->fromString('items?products%2Ename=a.b&ids[]=1&ids[]=2');

        self::assertSame(['products.name' => 'a.b', 'ids' => ['1', '2']], $uri->query);
    }
}
