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
}
