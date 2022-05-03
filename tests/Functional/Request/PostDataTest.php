<?php

declare(strict_types=1);

namespace Medas\HttpRequestHandlerTest\Functional\Request;

use Medas\HttpRequestHandler\Request\PostData;
use PHPUnit\Framework\TestCase;

class PostDataTest extends TestCase
{
    public function testArrayAccess(): void
    {
        $postData = new PostData(['a' => 'b']);

        self::assertEquals('b', $postData['a']);
    }
}
