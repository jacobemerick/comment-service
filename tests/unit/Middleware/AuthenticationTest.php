<?php

namespace Jacobemerick\CommentService\Middleware;

use PHPUnit_Framework_TestCase;

class AuthenticationTest extends PHPUnit_Framework_TestCase
{

    public function testIsInstanceOfAuthentication()
    {
        $authenticationMiddleware = new Authentication('', '');
        $this->assertInstanceOf(Authentication::class, $authenticationMiddleware);
    }

    public function testConstructSetsCredentials()
    {
        $username = 'test-user';
        $password = 'test-pass';
        $authenticationMiddleware = new Authentication($username, $password);

        $this->assertAttributeEquals($username, 'username', $authenticationMiddleware);
        $this->assertAttributeEquals($password, 'password', $authenticationMiddleware);
    }

    public function testInvokeSkipsApiDocsRoute()
    {
        $this->markTestIncomplete('todo');
    }

    public function testInvokeReturns403ForInvalidCredentials()
    {
        $this->markTestIncomplete('todo');
    }

    public function testInvokeReturnsCallbackForValidCredentials()
    {
        $this->markTestIncomplete('todo');
    }

    public function testGetAuthHeaderReturnsEncodedHeader()
    {
        $this->markTestIncomplete('todo');
    }
}
