<?php

namespace Jacobemerick\CommentService\Middleware;

use PHPUnit_Framework_TestCase;

class ParseJsonBodyTest extends PHPUnit_Framework_TestCase
{

    public function testIsInstanceOfParseJsonBody()
    {
        $parseJsonBodyMiddleware = new ParseJsonBody();
        $this->assertInstanceOf(ParseJsonBody::class, $parseJsonBodyMiddleware);
    }

    public function testInvokeBailsOnUnreadableBody()
    {
        $this->markTestIncomplete('todo');
    }

    public function testInvokeSkipsEmptyBody()
    {
        $this->markTestIncomplete('todo');
    }

    public function testInvokeBailsOnJsonError()
    {
        $this->markTestIncomplete('todo');
    }

    public function testInvokePassesParsedBodyToRequest()
    {
        $this->markTestIncomplete('todo');
    }
}
