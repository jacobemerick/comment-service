<?php

namespace Jacobemerick\CommentService\ErrorHandler;

use PHPUnit_Framework_TestCase;

class JsonResponseTest extends PHPUnit_Framework_TestCase
{

    public function testIsInstanceOfJsonResponse()
    {
        $errorHandler = new JsonResponse();
        $this->assertInstanceOf(JsonResponse::class, $errorHandler);
    }

    public function testInvokeWritesEncodedErrorToResponse()
    {
        $this->markTestIncomplete('todo');
    }

    public function testInvokeReturnsResponse()
    {
        $this->markTestIncomplete('todo');
    }
}
