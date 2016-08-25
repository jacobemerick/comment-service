<?php

namespace Jacobemerick\CommentService\Middleware;

use PHPUnit_Framework_TestCase;

class JsonHeaderTest extends PHPUnit_Framework_TestCase
{

    public function testIsInstanceOfJsonHeader()
    {
        $jsonHeaderMiddleware = new JsonHeader();
        $this->assertInstanceOf(JsonHeader::class, $jsonHeaderMiddleware);
    }

    public function testInvokeAddsJsonHeader()
    {
        $this->markTestIncomplete('todo');
    }

    public function testInvokeReturnsResponse()
    {
        $this->markTestIncomplete('todo');
    }
}
