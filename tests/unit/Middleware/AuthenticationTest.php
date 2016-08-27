<?php

namespace Jacobemerick\CommentService\Middleware;

use Exception;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UriInterface as Uri;
use ReflectionClass;

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
        $mockUri = $this->createMock(Uri::class);
        $mockUri->method('getPath')
            ->willReturn('/api-docs');

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getUri')
            ->willReturn($mockUri);
        $mockRequest->expects($this->never())
            ->method('getHeader');

        $mockResponseReturn = $this->createMock(Response::class);

        $mockResponse = $this->createMock(Response::class);

        $callable = function ($req, $res) use ($mockResponseReturn) {
            return $mockResponseReturn;
        };

        $response = (new Authentication('', ''))($mockRequest, $mockResponse, $callable);

        $this->assertSame($mockResponseReturn, $response);
    }

    public function testInvokeReturns403ForNoCredentials()
    {
        $mockUri = $this->createMock(Uri::class);
        $mockUri->method('getPath')
            ->willReturn('/path');

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getUri')
            ->willReturn($mockUri);
        $mockRequest->method('getHeader')
            ->willReturn([]);

        $mockResponseReturn = $this->createMock(Response::class);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->expects($this->once())
            ->method('withStatus')
            ->with(
                $this->equalTo(403)
            )
            ->willReturn($mockResponseReturn);

        $callable = function ($req, $res) {
            throw new Exception('callable was called');
        };

        $response = (new Authentication('', ''))($mockRequest, $mockResponse, $callable);

        $this->assertSame($mockResponseReturn, $response);
    }

    public function testInvokeReturns403ForInvalidCredentials()
    {
        $mockUri = $this->createMock(Uri::class);
        $mockUri->method('getPath')
            ->willReturn('/path');

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getUri')
            ->willReturn($mockUri);
        $mockRequest->method('getHeader')
            ->willReturn([ 'Invalid Auth' ]);

        $mockResponseReturn = $this->createMock(Response::class);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->expects($this->once())
            ->method('withStatus')
            ->with(
                $this->equalTo(403)
            )
            ->willReturn($mockResponseReturn);

        $callable = function ($req, $res) {
            return $mockResponseReturn;
        };

        $response = (new Authentication('', ''))($mockRequest, $mockResponse, $callable);

        $this->assertSame($mockResponseReturn, $response);
    }

    public function testInvokeReturnsCallbackForValidCredentials()
    {
        $username = 'test-user';
        $password = 'test-pass';

        $authHeader = base64_encode("{$username}:{$password}");
        $authHeader = "Basic {$authHeader}";

        $mockUri = $this->createMock(Uri::class);
        $mockUri->method('getPath')
            ->willReturn('/path');

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getUri')
            ->willReturn($mockUri);
        $mockRequest->method('getHeader')
            ->willReturn([ $authHeader ]);

        $mockResponseReturn = $this->createMock(Response::class);

        $mockResponse = $this->createMock(Response::class);
        $mockResponse->expects($this->never())
            ->method('withStatus');

        $callable = function ($req, $res) use ($mockResponseReturn) {
            return $mockResponseReturn;
        };

        $response = (new Authentication($username, $password))($mockRequest, $mockResponse, $callable);

        $this->assertSame($mockResponseReturn, $response);
    }

    public function testGetAuthHeaderReturnsEncodedHeader()
    {
        $username = 'test-user';
        $password = 'test-pass';

        $expectedAuthHeader = base64_encode("{$username}:{$password}");
        $expectedAuthHeader = "Basic {$expectedAuthHeader}";

        $authentication = new Authentication($username, $password);

        $reflectedGetAuthHeader = (new ReflectionClass($authentication))
            ->getMethod('getAuthHeader');
        $reflectedGetAuthHeader->setAccessible(true);

        $authHeader = $reflectedGetAuthHeader->invoke($authentication);

        $this->assertEquals($expectedAuthHeader, $authHeader);
    }
}
